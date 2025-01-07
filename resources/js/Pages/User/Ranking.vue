<script setup lang="ts">
import { computed } from 'vue'
import Divider from '@/components/Common/Divider.vue'
import { groupBy } from '@/Helpers/GroupBy'
import type { PageProps } from '@/Types/PageProps'
import { Head } from '@inertiajs/vue3'

const props = defineProps<{
  quiz: Quiz
  rankings: Ranking[]
} & PageProps>()

const grouped = computed(() => groupBy('points', props.rankings))
</script>

<template>
  <Head :title="`${quiz.title} - Ranking`" />

  <div class="w-full p-2 md:max-w-8xl">
    <Divider>
      <h1 class="font-bold text-xl text-primary text-center py-4 whitespace-nowrap">
        {{ quiz.title }} - Ranking
      </h1>
    </Divider>

    <div class="w-full flex justify-between text-sm font-semibold text-gray-900 border shadow bg-white rounded-md px-4 py-2 gap-x-2">
      <div class="sm:flex-none sm:w-full sm:max-w-56">
        Imię
      </div>

      <div class="flex-1">
        Nazwisko
      </div>

      <div class="flex-1">
        Szkoła
      </div>

      <div class="sm:flex-none sm:w-full max-w-16">
        Punkty
      </div>
    </div>

    <div
      v-for="(place, index) in grouped"
      :key="index"
      class="mt-4"
    >
      <div class="mt-2 bg-white border shadow rounded-md">
        <h1 class="text-white font-semibold border-b bg-primary rounded-t-md p-2 text-sm text-left">
          Miejsce {{ index + 1 }}
        </h1>

        <div
          v-for="ranking in place"
          :key="ranking.user.id"
          class="w-full flex justify-between text-sm text-gray-900 p-4 gap-x-2"
        >
          <div
            class="sm:flex-none sm:w-full sm:max-w-56"
            :class="[ranking.user.id === user?.id ? 'text-black' : 'text-gray-500']"
          >
            {{ ranking.user.id === user?.id ? user.firstname : ranking.user.firstname ?? '---' }}
          </div>

          <div
            class="flex-1"
            :class="[ranking.user.id === user?.id ? 'text-black' : 'text-gray-500']"
          >
            {{ ranking.user.id === user?.id ? user.surname : ranking.user.surname ?? '---' }}
          </div>

          <div
            class="flex-1"
            :class="[ranking.user.id === user?.id ? 'text-black' : 'text-gray-500']"
          >
            {{ ranking.user.school.name }}
          </div>

          <div
            class="sm:flex-none sm:w-full max-w-16 text-center"
            :class="[ranking.user.id === user?.id ? 'text-black' : 'text-gray-500']"
          >
            {{ ranking.points }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
