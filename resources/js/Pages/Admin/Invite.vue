<script setup lang="ts">
import { defineProps, ref } from 'vue'
import FormButton from '@/components/Common/FormButton.vue'
import CrudPage from '@/components/Crud/CrudPage.vue'
import Expand from '@/components/Common/Expand.vue'
import { type Errors } from '@inertiajs/core'
import ButtonFrame from '@/components/Common/ButtonFrame.vue'
import { keysWrapper } from '@/Helpers/KeysManager'

defineProps<{
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

const searchBarOptions = keysWrapper([{ text: 'Uczniowie' }, { text: 'Szkoły' }])
</script>

<template>
  <CrudPage :items="users" :options="options" :resource-name="`quizzes/${quiz.id}/invite`" :custom-queries="customQueries" :search-bar-options="searchBarOptions">
    <template #actions>
      <Expand />

      <ButtonFrame @click="selectedUsers = users.data.map(user => user.id)">Zaznacz wszystko</ButtonFrame>
      <FormButton :data="{ ids: selectedUsers }" method="post" :href="`/admin/quizzes/${quiz.id}/invite/assign`" preserve-scroll>Przypisz zaznaczonych</FormButton>
      <FormButton :data="{ ids: selectedUsers }" method="post" :href="`/admin/quizzes/${quiz.id}/invite/unassign`" preserve-scroll>Wypisz zaznaczonych</FormButton>
    </template>

    <template #item="{item}">
      <div
        class="flex p-5 bg-white/70 border-2 justify-between items-center rounded-xl shadow-sm cursor-pointer"
        :class="{ 'border-primary': selectedUsers.includes(item.id) }"
        @click="selectedUsers.includes(item.id) ? selectedUsers = selectedUsers.filter(id => id !== item.id) : selectedUsers.push(item.id)"
      >
        <div class="flex flex-col gap-1">
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
