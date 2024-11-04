<script setup lang="ts">
import { computed } from 'vue'
import type { Quiz } from '@/Types/Quiz'
import type { Ranking } from '@/Types/Ranking'
import Divider from '@/components/Common/Divider.vue'
import FormButton from '@/components/Common/FormButton.vue'
import { groupBy } from '@/Helpers/GroupBy'

const props = defineProps<{
  quiz: Quiz
  rankings: Ranking[]
}>()

const sorted = computed(() => [...props.rankings].toSorted((a, b) => `${a.user.name} ${a.user.surname}`.localeCompare(`${b.user.name} ${b.user.surname}`)))

const grouped = computed(() => groupBy('points', sorted.value))
</script>

<template>
  <div class="w-full p-2 md:max-w-8xl">
    <Divider>
      <h1 class="font-bold text-xl text-primary text-center p-4 whitespace-nowrap">
        {{ quiz.name }} - Ranking
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
            {{ ranking.user.name }}
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
        Wycofaj publikacje
      </FormButton>
    </div>
  </div>
</template>
