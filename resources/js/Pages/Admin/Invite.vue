<script setup lang="ts">
import { ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import { type Errors } from '@inertiajs/core'
import { CheckIcon } from '@heroicons/vue/20/solid'
import { CheckCircleIcon, MinusCircleIcon, UserPlusIcon } from '@heroicons/vue/24/outline'
import FormButton from '@/components/Common/FormButton.vue'
import CrudPage from '@/components/Crud/CrudPage.vue'
import Expand from '@/components/Common/Expand.vue'
import CustomCheckbox from '@/components/Common/CustomCheckbox.vue'
import { keysWrapper } from '@/Helpers/KeysManager'
import Dropdown from '@/components/Common/Dropdown.vue'

defineProps<{
  errors: Errors
  users: Pagination<User>
  quiz: Quiz
  assigned: number[]
  quizzes: Array<{ id: number, title: string }>
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
  { text: 'Według uczniów', name: 'user' },
  { text: 'Według szkół', name: 'school' },
]) as Mode[]

function changeQuiz(id: number) {
  router.get(`/admin/quizzes/${id}/invite`)
}
</script>

<template>
  <Head title="Zaproszenia - Panel administracyjny" />

  <CrudPage
    :items="users"
    :options="options"
    :resource-name="`quizzes/${quiz.id}/invite`"
    :search-bar-modes="searchBarModes"
  >
    <template #actions>
      <Dropdown
        pointer-position="left"
        :options="quizzes.map(item => ({ key: item.id, text: item.title }))"
        @option-click="(option: any) => changeQuiz(option.key)"
      >
        <div class="flex group items-center gap-2 p-2 hover:bg-primary/5 hover:text-primary rounded-lg duration-200 whitespace-nowrap">
          <UserPlusIcon class="icon stroke-gray-800 group-hover:stroke-primary" />

          <div class="flex text-gray-800 items-center">
            Zaproś do
          </div>

          <span class="font-bold  nowrap">
            {{ quiz.title }}
          </span>
        </div>
      </Dropdown>

      <Expand class="hidden sm:block" />
    </template>

    <template #itemsActions>
      <label class="flex gap-4 font-medium border-2 border-transparent">
        <CustomCheckbox
          :checked="selectedUsers.length > 0 && users.data.every(user=>selectedUsers.includes(user.id))"
          :some-checked="selectedUsers.length > 0"
          @check="checked => checked ? selectedUsers = users.data.map(user => user.id) : selectedUsers = []"
        />

        <span class="hidden md:block cursor-pointer select-none">
          {{
            selectedUsers.length == 0 ? 'Zaznacz wszystkich' : users.data.every(user=>selectedUsers.includes(user.id)) ? 'Odznacz wszystkich' : 'Odznacz zaznaczonych'
          }}
        </span>
      </label>

      <div class="flex gap-4">
        <FormButton
          button-class="!font-medium"
          text
          title="Przypisz zaznaczonych"
          method="post"
          :data="{ ids: selectedUsers }"
          :href="`/admin/quizzes/${quiz.id}/invite/assign`"
        >
          <CheckCircleIcon class="icon size-7 md:size-6" />

          <span class="hidden md:block">
            Przypisz zaznaczonych
          </span>
        </FormButton>

        <FormButton
          button-class="!font-medium"
          text
          title="Wypisz zaznaczonych"
          method="post"
          :data="{ ids: selectedUsers }"
          :href="`/admin/quizzes/${quiz.id}/invite/unassign`"
        >
          <MinusCircleIcon class="icon size-7 md:size-6" />

          <span class="hidden md:block">
            Wypisz zaznaczonych
          </span>
        </FormButton>
      </div>
    </template>

    <template #item="{item}">
      <div
        class="flex p-5 bg-white/70 border-2 justify-between items-center rounded-xl shadow-sm duration-200 transition-colors relative"
        :class="{ 'border-primary': selectedUsers.includes(item.id) }"
      >
        <div class="flex gap-4">
          <CustomCheckbox
            :checked="selectedUsers.includes(item.id)"
            @check="selectedUsers.includes(item.id) ? selectedUsers = selectedUsers.filter(id => id !== item.id) : selectedUsers.push(item.id)"
          />

          <div class="flex flex-col gap-1">
            <div class="w-full font-bold text-lg">
              {{ item.firstname }} {{ item.surname }}
            </div>

            <div class="text-gray-500">
              {{ item.school.name }}
            </div>
          </div>
        </div>

        <div class="flex justify-end items-center">
          <Transition>
            <div
              v-if="assigned.includes(item.id)"
              class="flex justify-center bg-primary size-full max-w-16 absolute items-center right-0 rounded-r-xl ring-2 ring-primary"
            >
              <CheckIcon class="size-6 text-white stroke-[3.5] rotate-6" />
            </div>
          </Transition>
        </div>
      </div>
    </template>
  </CrudPage>
</template>
