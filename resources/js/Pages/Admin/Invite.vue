<script setup lang="ts">
import { defineProps, ref } from 'vue'
import { type  User } from '@/Types/User'
import { type Pagination } from '@/Types/Pagination'
import FormButton from '@/components/Common/FormButton.vue'
import { type Quiz } from '@/Types/Quiz'
import { useForm } from '@inertiajs/vue3'

const sortOptions = [
  {
    name: 'Sortuj po ID',
    value: 'ID',
  },
  {
    name: 'Sortuj po Imieniu (A-Z)',
    value: 'name-asc',
  },
  {
    name: 'Sortuj po Imieniu (Z-A)',
    value: 'name-desc',
  },
  { name: 'Sortuj po Nazwisku (A-Z)',
    value: 'surname-asc',
  },
  {
    name: 'Sortuj po Szkole (A-Z)',
    value: 'school-asc',
  },
  {
    name: 'Sortuj po Szkole (Z-A)',
    value: 'school-desc',
  },
]

const props = defineProps<{
  users: Pagination<User>
  quiz: Quiz
  schools: {
    id: number
    name: string
    city: string
  }
  filters: {
    search: string
    sort: string
    schoolId: number | null
  }
}>()


const form = useForm({
  search: props.filters.search || '',
  sort: props.filters.sort || 'id',
  schoolId: props.filters.schoolId ?? null,
})

const selectedUserIds = ref<number[]>([])

const toggleUserSelection = (userId: number) => {
  if (selectedUserIds.value.includes(userId)) {
    selectedUserIds.value = selectedUserIds.value.filter(id => id !== userId)
  } else {
    selectedUserIds.value.push(userId)
  }
}
</script>

<template>
  <h1>Quiz: {{ quiz.name }}</h1>
  Wyszukiwarka użytkowników do zapraszania do testów

  <form @submit.prevent="form.get(`/admin/quizzes/${quiz.id}/invite`, { preserveState: true, replace: true })">
    <div>
      <label for="search">Wyszukaj użytkownika:</label>
      <input v-model="form.search" type="text" placeholder="Wpisz nazwę użytkownika">

      <label for="sort">Sortowanie:</label>
      <select v-model="form.sort" class="sorting-dropdown">
        <option v-for="option in sortOptions" :key="option.value" :value="option.value">
          {{ option.name }}
        </option>
      </select>

      <label for="school">Grupuj według szkoły:</label>
      <select v-model="form.schoolId" class="school-dropdown">
        <option :value="null">Wszystkie szkoły</option>
        <option v-for="school in props.schools" :key="school.id" :value="school.id">
          {{ school.name }}, {{ school.city }}
        </option>
      </select>

      <button type="submit">Apply Filters</button>
    </div>
  </form>

  <div>
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
