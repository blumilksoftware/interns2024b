<script setup lang="ts">
import { ref } from 'vue'
import FormButton from '@/components/Common/FormButton.vue'
import CrudPage from '@/components/Crud/CrudPage.vue'
import Expand from '@/components/Common/Expand.vue'
import { type Errors } from '@inertiajs/core'
import { keysWrapper } from '@/Helpers/KeysManager'
import Button from '@/components/Common/Button.vue'
import { CheckIcon } from '@heroicons/vue/24/outline'

defineProps<{
  errors: Errors
  users: Pagination<User>
  quiz: number
  schools: Array<{
    id: number
    name: string
    city: string
  }>
  assigned: number[]
}>()

const selectedUsers = ref<number[]>([])

const options: SortOption[] = [
  { text: 'Po id (rosnąco)', key: 'id' },
  { text: 'Po id (malejąco)', key: 'id', desc: true },
  { text: 'Po nazwie (A–Z)', key: 'name' },
  { text: 'Po nazwie (Z–A)', key: 'name', desc: true },
  { text: 'Po szkole (A-Z)', key: 'school' },
  { text: 'Po szkole (Z-A)', key: 'school', desc: true },
]

const searchBarModes = keysWrapper([
  { text: 'Uczniowie', name: 'user' },
  { text: 'Szkoły', name: 'school' },
]) as Mode[]
</script>

<template>
  <CrudPage
    :items="users"
    :options="options"
    :resource-name="`quizzes/${quiz}/invite`"
    :search-bar-modes="searchBarModes"
  >
    <template #actions>
      <Expand class="hidden sm:block" />

      <Button @click="selectedUsers = users.data.map(user => user.id)">
        Zaznacz wszystko
      </Button>

      <FormButton
        :data="{ ids: selectedUsers }"
        method="post"
        :href="`/admin/quizzes/${quiz}/invite/assign`"
        preserve-scroll
      >
        Przypisz zaznaczonych
      </FormButton>

      <FormButton
        :data="{ ids: selectedUsers }"
        method="post"
        :href="`/admin/quizzes/${quiz}/invite/unassign`"
        preserve-scroll
      >
        Wypisz zaznaczonych
      </FormButton>
    </template>

    <template #item="{item}">
      <div
        class="flex p-5 bg-white/70 border-2 justify-between items-center rounded-xl shadow-sm cursor-pointer duration-200 transition-colors"
        :class="{ 'border-primary': selectedUsers.includes(item.id) }"
        @click="selectedUsers.includes(item.id) ? selectedUsers = selectedUsers.filter(id => id !== item.id) : selectedUsers.push(item.id)"
      >
        <div class="flex gap-4">
          <div class="flex flex-col gap-1">
            <div class="w-full font-bold text-lg">
              {{ item.firstname }} {{ item.surname }}
            </div>

            <div class="text-gray-500">
              {{ item.school.name }}
            </div>
          </div>
        </div>

        <div v-if="assigned.includes(item.id)">
          <CheckIcon class="icon" />
        </div>
      </div>
    </template>
  </CrudPage>
</template>
