<script setup lang="ts">
import { computed, ref } from 'vue'
import Divider from '@/components/Common/Divider.vue'
import FormButton from '@/components/Common/FormButton.vue'
import { groupBy } from '@/Helpers/GroupBy'
import { type PageProps } from '@/Types/PageProps'
import ModalWindow from '@/components/Common/ModalWindow.vue'
import Dropdown from '@/components/Common/Dropdown.vue'
import { UserPlusIcon } from '@heroicons/vue/24/outline'
import Button from '@/components/Common/Button.vue'
import { Head, router } from '@inertiajs/vue3'

const props = defineProps<{
  quiz: Quiz
  quizzes: Array<{ id: number, title: string }>
  rankings: Ranking[]
} & PageProps>()

const sorted = computed(() => [...props.rankings].toSorted((a, b) => `${a.user.firstname} ${a.user.surname}`.localeCompare(`${b.user.firstname} ${b.user.surname}`)))
const grouped = computed(() => groupBy('points', sorted.value))
const showInviteModal = ref(false)
const inviteTo = ref<{ id: number, title: string }>()

function openGroupInvite(key: number) {
  inviteTo.value = props.quizzes.find(quiz => quiz.id == key)
  router.get(`/admin/quizzes/${props.quiz.id}/ranking/invite/${key}`)
}
</script>

<template>
  <Head :title="`${quiz.title} - Ranking`" />

  <div class="w-full p-2 md:max-w-8xl">
    <Divider>
      <h1 class="font-bold text-xl text-primary text-center py-4 whitespace-nowrap">
        {{ quiz.title }} - Ranking
      </h1>
    </Divider>

    <div class="w-full flex justify-between text-sm font-semibold text-gray-900 border shadow bg-white rounded-md px-4 py-2 gap-x-1">
      <div class="flex-1 sm:flex-none sm:w-full sm:max-w-56">
        Imię
      </div>

      <div class="flex-1">
        Nazwisko
      </div>

      <div class="flex-1">
        Szkoła
      </div>

      <div class="flex-none w-full max-w-16">
        Punkty
      </div>
    </div>

    <div
      v-for="(place, index) in grouped"
      :key="index"
      class="mt-4"
    >
      <div class="mt-2 bg-white border shadow rounded-md">
        <h1 class="text-white bg-primary font-semibold border-b rounded-t-md p-2 text-sm text-left">
          Miejsce {{ index + 1 }}
        </h1>

        <div
          v-for="ranking in place"
          :key="ranking.user.id"
          class="w-full flex justify-between text-sm text-gray-900 p-4 gap-x-1"
        >
          <div class="flex-1 sm:flex-none sm:w-full sm:max-w-56">
            {{ ranking.user.firstname }}
          </div>

          <div class="flex-1">
            {{ ranking.user.surname }}
          </div>

          <div class="flex-1">
            {{ ranking.user.school.name }}
          </div>

          <div class="flex-none w-full max-w-16 text-center">
            {{ ranking.points }}
          </div>
        </div>
      </div>
    </div>


    <div class="flex gap-4 p-4 pl-0">
      <FormButton
        :disabled="quiz.isRankingPublished || rankings.length == 0"
        method="post"
        :href="`/admin/quizzes/${quiz.id}/ranking/publish`"
        small
        preserve-scroll
      >
        Publikuj
      </FormButton>

      <FormButton
        :disabled="!quiz.isRankingPublished"
        method="post"
        :href="`/admin/quizzes/${quiz.id}/ranking/unpublish`"
        small
        preserve-scroll
      >
        Wycofaj publikację
      </FormButton>

      <Button
        small
        preserve-scroll
        @click="showInviteModal = true"
      >
        Zaproś do innego testu
      </Button>
    </div>
  </div>

  <ModalWindow
    v-model="showInviteModal"
    title="Zaproś do testu"
  >
    <Dropdown
      pointer-position="left"
      :options="quizzes.map(item => ({ key: item.id, text: item.title }))"
      @option-click="(option: any) => openGroupInvite(option.key)"
    >
      <div class="flex group items-center gap-2 p-2 hover:bg-primary/5 hover:text-primary rounded-lg duration-200 whitespace-nowrap">
        <UserPlusIcon class="icon stroke-gray-800 group-hover:stroke-primary" />

        <div class="flex text-gray-800 items-center">
          Zaproś do
        </div>

        <span class="font-bold  nowrap">
          {{ inviteTo?.title ?? "Brak" }}
        </span>
      </div>
    </Dropdown>
  </ModalWindow>
</template>
