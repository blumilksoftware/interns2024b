<script setup lang="ts">
import { defineProps, ref, computed } from 'vue'
import FormButton from '@/components/Common/FormButton.vue'
import { useForm } from '@inertiajs/vue3'
import { useParams } from '@/Helpers/Params'

const sortOptions = [
  {
    name: 'ID',
    value: 'id',
  },
  {
    name: 'Imię',
    value: 'name',
  },
  {
    name: 'Szkoła',
    value: 'school',
  },
]

const orderOptions = [
  { name: 'Rosnąco',
    value: 'asc',
  },
  { name: 'Malejąco',
    value: 'desc',
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
  assigned: number[]
}>()

const params = useParams()

const form = useForm({
  search: params.name ?? '',
  sort: params.sort ?? 'id',
  order: params.order ?? 'asc',
  schoolId: params.schoolId ?? null,
  limit: params.limit ?? null,
})

const selectedUserIds = ref<number[]>([])

const toggleUserSelection = (userId: number) => {
  if (selectedUserIds.value.includes(userId)) {
    selectedUserIds.value = selectedUserIds.value.filter(id => id !== userId)
  } else {
    selectedUserIds.value.push(userId)
  }
}

const showPagination = computed(() => {
  return !params.limit
})
</script>

<template>
  <h1>Quiz: {{ quiz.title }}</h1>
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

      <label for="order">Kierunek:</label>
      <select v-model="form.order" class="order-dropdown">
        <option v-for="option in orderOptions" :key="option.value" :value="option.value">
          {{ option.name }}
        </option>
      </select>

      <label for="school">Filtruj po szkołe:</label>
      <select v-model="form.schoolId" class="school-dropdown">
        <option :value="null">Wszystkie szkoły</option>
        <option v-for="school in props.schools" :key="school.id" :value="school.id">
          {{ school.name }}, {{ school.city }}
        </option>
      </select>

      <label for="limit">Liczba wyników:</label>
      <input v-model.number="form.limit" type="number" min="1" placeholder="Wpisz liczbę wyników">


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
          <th>Status</th>
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
          <td>{{ assigned.includes(user.id) }}</td>
        </tr>
      </tbody>
    </table>

    <div v-if="showPagination">
      <FormButton :disabled="!users.links.prev" method="get" :href="users.links.prev" preserve-scroll small>Poprzednia</FormButton>
      <FormButton :disabled="!users.links.next" method="get" :href="users.links.next" preserve-scroll small>Następna</FormButton>
    </div>
    <FormButton :data="{ ids: selectedUserIds }" method="post" :href="`/admin/quizzes/${quiz.id}/invite/assign`" preserve-scroll>Przypisz zaznaczonych użytkowników</FormButton>
    <FormButton :data="{ ids: selectedUserIds }" method="post" :href="`/admin/quizzes/${quiz.id}/invite/unassign`" preserve-scroll>Wypisz zaznaczonych użytkowników</FormButton>
  </div>
</template>
