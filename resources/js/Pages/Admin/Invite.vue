<script setup lang="ts">
import { defineProps, ref, computed } from 'vue'
import { type  User } from '@/Types/User'
import { type Pagination } from '@/Types/Pagination'
import FormButton from '@/components/Common/FormButton.vue'
import { type Quiz } from '@/Types/Quiz'
import { useForm } from '@inertiajs/vue3'
import Divider from '@/components/Common/Divider.vue'
import Button from '@/components/Common/Button.vue'

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
    name: 'Nazwisko',
    value: 'surname',
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
  schools: Array<{
    id: number
    name: string
    city: string
  }>
  filters: {
    search: string
    sort: string
    order: string
    schoolId: number | null
    limit: number | null
  }
}>()

const form = useForm({
  search: props.filters.search ?? '',
  sort: props.filters.sort ?? 'id',
  order: props.filters.order ?? 'asc',
  schoolId: props.filters.schoolId ?? null,
  limit: props.filters.limit ?? null,

})

const selectedUserIds = ref<number[]>([])

const showPagination = computed(() => {
  return props.filters.limit === null
})

const indeterminate = computed(() => selectedUserIds.value.length > 0 && selectedUserIds.value.length < props.users.data.length)
</script>

<template>
  <div class="w-full p-2 md:max-w-8xl">
    <Divider>
      <h1 class="font-bold text-xl text-primary text-center p-4 whitespace-nowrap">{{ quiz.title }} - Przypisywanie użytkowników</h1>
    </Divider>

    <form
      class="bg-white shadow border rounded-md p-4 grid grid-cols-2 gap-4"
      @submit.prevent="form.get(`/admin/quizzes/${quiz.id}/invite`, { preserveState: true, replace: true })"
    >
      <div>
        <label class="font-semibold text-nowrap block" for="search">Wyszukaj użytkownika:</label>
        <input v-model="form.search" class="border w-full border-black p-2 bg-white rounded-md" type="text" placeholder="Wpisz nazwę użytkownika">
      </div>

      <div>
        <label class="font-semibold text-nowrap block" for="sort">Sortowanie:</label>
        <select v-model="form.sort" class="sorting-dropdown border border-black p-2 bg-white rounded-md w-full">
          <option v-for="option in sortOptions" :key="option.value" :value="option.value">
            {{ option.name }}
          </option>
        </select>
      </div>

      <div>
        <label class="font-semibold text-nowrap block" for="order">Kierunek:</label>
        <select v-model="form.order" class="order-dropdown border border-black p-2 bg-white rounded-md w-full">
          <option v-for="option in orderOptions" :key="option.value" :value="option.value">
            {{ option.name }}
          </option>
        </select>
      </div>

      <div>
        <label class="font-semibold text-nowrap block" for="school">Grupuj według szkoły:</label>
        <select v-model="form.schoolId" class="school-dropdown border border-black p-2 bg-white rounded-md w-full">
          <option :value="null">Wszystkie szkoły</option>
          <option v-for="school in props.schools" :key="school.id" :value="school.id">
            {{ school.name }}, {{ school.city }}
          </option>
        </select>
      </div>

      <div>
        <label class="font-semibold text-nowrap block" for="limit">Liczba wyników:</label>
        <input v-model.number="form.limit" class="school-dropdown border border-black p-2 bg-white rounded-md w-full" type="number" min="1" placeholder="Wpisz liczbę wyników">
      </div>

      <Button type="submit">Apply Filters</Button>
    </form>

    <div class="bg-white shadow border rounded-md p-4 mt-8 flow-root">
      <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
          <div class="relative">
            <div v-if="selectedUserIds.length > 0" class="absolute left-14 top-0 flex h-12 items-center space-x-3 bg-white sm:left-12">
              <FormButton extra-small :data="{ ids: selectedUserIds }" method="post" :href="`/admin/quizzes/${quiz.id}/invite`" preserve-scroll>Zaproś [MAIL]</FormButton>
              <FormButton extra-small :data="{ ids: selectedUserIds }" method="post" :href="`/admin/quizzes/${quiz.id}/invite/assign`" preserve-scroll>Przypisz [NOMAIL]</FormButton>
            </div>
            <table class="min-w-full table-fixed divide-y divide-gray-300">
              <thead>
                <tr>
                  <th scope="col" class="relative px-7 sm:w-12 sm:px-6">
                    <input type="checkbox" class="absolute left-4 top-1/2 -mt-2 size-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" :checked="indeterminate || selectedUserIds.length === users.data.length" :indeterminate="indeterminate" @change="selectedUserIds = $event.target.checked ? users.data.map(({id}) => id) : []">
                  </th>
                  <th scope="col" class="min-w-48 py-3.5 pr-3 text-left text-sm font-semibold text-gray-900">ID</th>
                  <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Imię</th>
                  <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Nazwisko</th>
                  <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Szkoła</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 bg-white">
                <tr v-for="user in users.data" :key="user.id" :class="[selectedUserIds.includes(user.id) && 'bg-gray-50']">
                  <td class="relative px-7 sm:w-12 sm:px-6">
                    <div v-if="selectedUserIds.includes(user.id)" class="absolute inset-y-0 left-0 w-0.5 bg-indigo-600" />
                    <input v-model="selectedUserIds" type="checkbox" class="absolute left-4 top-1/2 -mt-2 size-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" :value="user.id">
                  </td>
                  <td :class="['whitespace-nowrap py-4 pr-3 text-sm font-medium', selectedUserIds.includes(user.id) ? 'text-indigo-600' : 'text-gray-900']">
                    {{ user.id }}
                  </td>
                  <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                    {{ user.name }}
                  </td>
                  <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                    {{ user.surname }}
                  </td>
                  <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                    {{ user.school.name }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div v-if="showPagination" class="w-full flex gap-4">
        <FormButton :disabled="!users.links.prev" method="get" :href="users.links.prev ?? ''" preserve-scroll small>Poprzednia</FormButton>
        <FormButton :disabled="!users.links.next" method="get" :href="users.links.next ?? ''" preserve-scroll small>Następna</FormButton>
      </div>
    </div>
  </div>
</template>
