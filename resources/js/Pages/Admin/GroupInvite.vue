<script setup lang="ts">
import { computed, ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import { type Errors } from '@inertiajs/core'
import { CheckIcon, UsersIcon } from '@heroicons/vue/20/solid'
import { CheckCircleIcon, MinusCircleIcon, UserPlusIcon, TrophyIcon } from '@heroicons/vue/24/outline'
import FormButton from '@/components/Common/FormButton.vue'
import Expand from '@/components/Common/Expand.vue'
import CustomCheckbox from '@/components/Common/CustomCheckbox.vue'
import { keysWrapper } from '@/Helpers/KeysManager'
import Dropdown from '@/components/Common/Dropdown.vue'
import { groupBy } from '@/Helpers/GroupBy'
import SearchBar from '@/components/Crud/SearchBar.vue'

const props = defineProps<{
  errors: Errors
  rankings: Ranking[]
  from: Quiz
  to: Quiz
  assigned: number[]
  fromQuizzes: Array<{ id: number, title: string }>
  toQuizzes: Array<{ id: number, title: string }>
}>()

const limitToFirstThree = ref(true)
const searchbar = ref<{value: string, mode: 'user' | 'school' }>()

const searchBarModes = keysWrapper([
  { text: 'Według uczniów', name: 'user' },
  { text: 'Według szkół', name: 'school' },
]) as Mode[]

function showOnlyFirstThreePlaces(group: InviteGroup) {
  if (!limitToFirstThree.value) {
    return group
  }

  const scores = Array.from(new Set(group.users.map(user => user.points).toSorted((a, b) => b-a)))
  const minimalScore = scores.slice(0, 3).at(-1)

  if (minimalScore === undefined) {
    return group
  }

  return { ...group, users: group.users.filter(user => user.points >= minimalScore) }
}

function applySchoolSearch(group: InviteGroup) {
  if (!searchbar.value || searchbar.value.mode !== 'school' || !searchbar.value?.value) {
    return true
  }

  const text = searchbar.value.value.toLowerCase().trim()

  return group.school.name.toLowerCase().includes(text) || group.school.city.toLowerCase().includes(text)
}

function applyUserSearch(group: InviteGroup) {
  if (!searchbar.value || searchbar.value.mode !== 'user' || !searchbar.value?.value) {
    return group
  }

  const text = searchbar.value.value.toLowerCase().trim()

  return{ ...group, users: group.users.filter(({ user }) => `${user.firstname} ${user.surname}`.toLowerCase().includes(text)) }
}

function hideEmptyGroups(group: InviteGroup) {
  return group.users.length > 0
}

const grouped = computed(() => {
  const sorted = props.rankings
    .toSorted((a, b) => b.points - a.points)
    .map(item => ({ ...item, school_id: item.user.school.id }))

  const grouped = groupBy('school_id', sorted).map(item => ({
    users: item,
    school: item[0].user.school,
    points: item.reduce((value, user) => value + user.points, 0),
  }))

  return grouped
    .map(showOnlyFirstThreePlaces)
    .filter(applySchoolSearch)
    .map(applyUserSearch)
    .filter(hideEmptyGroups)
})

const selectedUsers = ref<number[]>([])

function changeQuiz(from: number | string, to: number | string) {
  router.get(`/admin/quizzes/${from}/ranking/invite/${to}`)
}

function search(text?: string, mode?: string) {
  if (text && mode) {
    searchbar.value = { value: text, mode: mode as 'user' | 'school' }
  }
  else {
    searchbar.value = undefined
  }
}
</script>

<template>
  <Head title="Zaproszenia - Panel administracyjny" />

  <div class="flex flex-col w-full pb-3">
    <div
      data-name="toolbar"
      class="flex flex-col xs:flex-row px-4 gap-2"
    >
      <Dropdown
        pointer-position="left"
        :options="fromQuizzes.map(item => ({ key: item.id, text: item.title }))"
        @option-click="(option: any) => changeQuiz(option.key, to.id)"
      >
        <div class="flex group items-center gap-2 p-2 xs:pr-0 hover:bg-primary/5 hover:text-primary rounded-lg duration-200 whitespace-nowrap">
          <UserPlusIcon class="icon stroke-gray-800 group-hover:stroke-primary" />

          <div class="flex text-gray-800 items-center">
            Zaproś z
          </div>

          <span class="font-bold  nowrap">
            {{ from.title }}
          </span>
        </div>
      </Dropdown>

      <Dropdown
        pointer-position="left"
        :options="toQuizzes.map(item => ({ key: item.id, text: item.title }))"
        @option-click="(option: any) => changeQuiz(from.id, option.key)"
      >
        <div class="flex group items-center gap-2 p-2 xs:pl-0 hover:bg-primary/5 hover:text-primary rounded-lg duration-200 whitespace-nowrap">
          <UserPlusIcon class="icon stroke-gray-800 group-hover:stroke-primary xs:hidden" />

          <div class="flextext-gray-800 items-center xs:hidden">
            Zaproś do
          </div>

          <div class="flextext-gray-800 items-cente hidden xs:block">
            do
          </div>

          <span class="font-bold nowrap">
            {{ to.title }}
          </span>
        </div>
      </Dropdown>

      <Expand class="hidden sm:block" />

      <button
        :title="`${limitToFirstThree ? 'Wyświetl tylko uczniów na podium' : 'Pokaż wszystkich uczestników'}`"
        class="flex gap-2 hover:bg-primary/5 hover:text-primary duration-200 p-2 rounded-lg"
        @click="limitToFirstThree = !limitToFirstThree"
      >
        <TrophyIcon
          v-if="limitToFirstThree"
          class="size-6"
        />

        <UsersIcon
          v-else
          class="size-6"
        />

        <span class="hidden sm:block">
          {{ limitToFirstThree ? 'Wyświetl tylko uczniów na podium' : 'Pokaż wszystkich uczestników' }}
        </span>
      </button>
    </div>

    <div class="flex w-full px-4 mt-2 justify-between gap-2">
      <SearchBar
        class="w-full"
        default-value=""
        :modes="searchBarModes"
        @search="search"
      />
    </div>

    <div class="flex gap-4 pt-4 px-9 justify-between items-center">
      <label class="flex gap-4 font-medium border-2 border-transparent">
        <CustomCheckbox
          :checked="selectedUsers.length > 0 && rankings.every(ranking=>selectedUsers.includes(ranking.user.id))"
          :some-checked="selectedUsers.length > 0"
          @check="checked => checked ? selectedUsers = rankings.map(ranking => ranking.user.id) : selectedUsers = []"
        />

        <span class="hidden md:block cursor-pointer select-none">
          {{
            selectedUsers.length == 0 ? 'Zaznacz wszystkich' : rankings.every(ranking=>selectedUsers.includes(ranking.user.id)) ? 'Odznacz wszystkich' : 'Odznacz zaznaczonych'
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
          :href="`/admin/quizzes/${to.id}/invite/assign`"
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
          :href="`/admin/quizzes/${to.id}/invite/unassign`"
        >
          <MinusCircleIcon class="icon size-7 md:size-6" />

          <span class="hidden md:block">
            Wypisz zaznaczonych
          </span>
        </FormButton>
      </div>
    </div>

    <div class="flex flex-col gap-4 p-4">
      <div
        v-for="ranking of grouped"
        :key="ranking.school.id"
        class="flex flex-col gap-4 p-4"
      >
        <div class="w-full flex flex-row items-center xs:px-5 px-0">
          <div class="mr-1 md:mr-4 w-7 border-t border-primary duration-200 transition-colors" />

          <h1 class="font-bold text-xl text-primary text-center w-1/2 md:whitespace-nowrap md:w-min py-4">
            {{ ranking.school.name }}
          </h1>

          <div class="ml-1 md:mx-7 flex-1 w-full border-t border-primary duration-200 transition-colors" />
        </div>

        <div
          v-for="item of ranking.users"
          :key="item.user.id"
          class="flex p-5 bg-white/70 border-2 justify-between items-center rounded-xl shadow-sm duration-200 transition-colors relative"
          :class="{ 'border-primary': selectedUsers.includes(item.user.id) }"
        >
          <div class="flex gap-4">
            <CustomCheckbox
              :checked="selectedUsers.includes(item.user.id)"
              @check="selectedUsers.includes(item.user.id) ? selectedUsers = selectedUsers.filter(id => id !== item.user.id) : selectedUsers.push(item.user.id)"
            />

            <div class="flex flex-col gap-1">
              <div class="w-full font-bold text-lg">
                {{ item.user.firstname }} {{ item.user.surname }}
              </div>

              <div class="text-gray-500">
                Wynik: {{ item.points }}
              </div>
            </div>
          </div>

          <div class="flex justify-end items-center">
            <Transition>
              <div
                v-if="assigned.includes(item.user.id)"
                class="flex justify-center bg-primary size-full max-w-16 absolute items-center right-0 rounded-r-xl ring-2 ring-primary"
              >
                <CheckIcon class="size-6 text-white stroke-[3.5] rotate-6" />
              </div>
            </Transition>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
