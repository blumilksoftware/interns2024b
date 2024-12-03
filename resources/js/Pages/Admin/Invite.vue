<script setup lang="ts">
import { computed, defineProps, ref, watch } from 'vue'
import FormButton from '@/components/Common/FormButton.vue'
import CrudPage from "@/components/Crud/CrudPage.vue"
import Expand from "@/components/Common/Expand.vue"
import Searchbar from "@/components/Common/Searchbar.vue"
import { nanoid } from "nanoid"
import { type Errors } from '@inertiajs/core'
import ButtonFrame from "@/components/Common/ButtonFrame.vue";

const props = defineProps<{
  errors: Errors
  users: Pagination<User>
  quiz: Quiz
  schools: Array<{
    id: number
    name: string
    city: string
  }>
  assigned: number[]
}>()

const selectedUsers = ref<number[]>([])

const schoolId = ref<number>()
const filteredSchools = computed(() => props.schools.toSorted((a, b) => (a.city + a.name).localeCompare(b.city + b.name)))
const filteredSchoolOptions = computed(() =>
  [
    { id: -1, key: nanoid(), title: 'Nie filtruj', text: '' },
    ...filteredSchools.value.map((school): Option & { id: number } =>
      ({ id: school.id, key: nanoid(), title: school.city, text: school.name }),
    ),
  ],
)

function customQueries(): string[] {
  if (schoolId.value && schoolId.value !== -1) {
    return [`schoolId=${schoolId.value}`]
  }

  return []
}

const options: SortOption[] = [
  {text: 'Po id (rosnąco)', key: 'id'},
  {text: 'Po id (malejąco)', key: 'id', desc: true},
  {text: 'Po nazwie (A–Z)', key: 'name'},
  {text: 'Po nazwie (Z–A)', key: 'name', desc: true},
  {text: 'Po szkole (A-Z)', key: 'school'},
  {text: 'Po szkole (Z-A)', key: 'school', desc: true},
]
</script>

<template>
  <CrudPage :items="users" :options="options" :resource-name="`quizzes/${quiz.id}/invite`" :custom-queries="customQueries">
    <template #actions>
      <Expand />

      <ButtonFrame @click="selectedUsers = users.data.map(user => user.id)">Zaznacz wszystko</ButtonFrame>
      <FormButton :data="{ ids: selectedUsers }" method="post" :href="`/admin/quizzes/${quiz.id}/invite/assign`" preserve-scroll>Przypisz zaznaczonych</FormButton>
      <FormButton :data="{ ids: selectedUsers }" method="post" :href="`/admin/quizzes/${quiz.id}/invite/unassign`" preserve-scroll>Wypisz zaznaczonych</FormButton>
    </template>

    <template #searchActions>
      <Searchbar
        label="Szkoła"
        aria-label="Poszukiwanie szkół. Otwiera listę szkół"
        :options="filteredSchoolOptions"
        :error="errors.school_id"
        @change="schoolId = $event.id"
      />
    </template>

    <template #item={item}>
      <div
        class="flex gap-1 p-4 bg-white/70 border-2 justify-between items-center rounded-xl shadow-sm"
        :class="{ 'border-primary': selectedUsers.includes(item.id) }"
        @click="selectedUsers.includes(item.id) ? selectedUsers = selectedUsers.filter(id => id !== item.id) : selectedUsers.push(item.id)"
      >
        <div>
          <div class="w-full font-bold text-lg">
            {{ item.firstname }} {{ item.surname }}
          </div>
          <div class="text-gray-500">{{ item.school.name }}</div>
        </div>

        <div class="">{{ assigned.includes(item.id) ? "Tak" : "Nie" }}</div>
      </div>
    </template>
  </CrudPage>
</template>
