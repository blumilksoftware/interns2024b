<script setup lang="ts">
import { defineProps, ref, watch } from 'vue'
import { type  User } from '@/Types/User'
import { type Pagination } from '@/Types/Pagination'
import FormButton from '@/components/Common/FormButton.vue'
import { type Quiz } from '@/Types/Quiz'
import { useForm } from '@inertiajs/vue3'

const sortOptions = [
  {
    name: 'Sort by Name (A-Z)',
    value: 'name-asc',
  },
  {
    name: 'Sort by Name (Z-A)',
    value: 'name-desc',
  },
  { name: 'Sort by Surname (A-Z)',
    value: 'surname-asc',
  },
  {
    name: 'Sort by Surname (Z-A)',
    value: 'surname-desc',
  },
]

const props = defineProps<{
  users: Pagination<User>
  quiz: Quiz
  filters: {
    search: string
    sort: string
  }
}>()


const form = useForm({
  search: props.filters.search || '',
  sort: props.filters.sort || 'name-asc',
})

const selectedUserIds = ref<number[]>([])

const toggleUserSelection = (userId: number) => {
  if (selectedUserIds.value.includes(userId)) {
    selectedUserIds.value = selectedUserIds.value.filter(id => id !== userId)
  } else {
    selectedUserIds.value.push(userId)
  }
}

watch(() => [form.search, form.sort], () => {
  form.get(`/admin/quizzes/${props.quiz.id}/invite`, {
    preserveState: true,
    replace: true,
  })
}, { immediate: true })
</script>

<template>
  <h1>Quiz: {{ quiz.name }}</h1>
  Wszukiwarka użytkowników do zapraszania do testów

  <div>
    <select v-model="form.sort" class="sorting-dropdown">
      <option v-for="option in sortOptions" :key="option.value" :value="option.value">
        {{ option.name }}
      </option>
    </select>
    <table>
      <thead>
        <tr>
          <th />
          <th>ID Użytkownika</th>
          <th>Imię</th>
          <th>Nazwisko</th>
          <th>Szkoła</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="user in users.data" :key="user.id">
          <td>
            <input
              type="checkbox"
              :value="user.id"
              @change="toggleUserSelection(user.id)"
            >
          </td>
          <td>{{ user.id }}</td>
          <td>{{ user.name }}</td>
          <td>{{ user.surname }}</td>
          <td>{{ user.school.name }}</td>
        </tr>
      </tbody>
    </table>

    <FormButton :disabled="!users.links.prev" method="get" :href="users.links.prev" small>Poprzednia</FormButton>
    <FormButton :disabled="!users.links.next" method="get" :href="users.links.next" small>Następna</FormButton>
    <FormButton :data="{ ids: selectedUserIds }" method="post" :href="`/admin/quizzes/${quiz.id}/invite`">Zaproś zaznaczonych użytkowników</FormButton>
  </div>
</template>
