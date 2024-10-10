<script setup lang="ts">
import {computed, ref} from 'vue'
import type { QuizRankingProps} from '@/Types/Ranking'
import Divider from '@/components/Common/Divider.vue'
import type Ranking from '@/Pages/User/Ranking.vue'
import {type PageProps} from '@/Types/PageProps'

const props = defineProps<QuizRankingProps & PageProps>()

const quiz = ref(props.quiz)

const sortedRankings = computed(() => {
  return [...props.rankings].toSorted((a, b) => b.points - a.points)
})

const groupedRanking = computed(() => Object.values<Ranking[]>(
  sortedRankings.value
    .reduce((prev, current) => ({ ...prev, [current.points]: [...(prev[current.points] || []), current] }), {}),
).reverse())

</script>

<template>
  <div class="w-full p-2 md:max-w-8xl">
    <Divider>
      <h1 class="font-bold text-xl text-primary text-center p-4 whitespace-nowrap">Ranking Quizu: {{ quiz.name }}</h1>
    </Divider>

    <div class="p-4 bg-white border shadow rounded-md">
      <div class="flow-root">
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
          <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
            <table class="min-w-full">
              <thead class="bg-white">
                <tr>
                  <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Imię</th>
                  <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Nazwisko</th>
                  <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Szkoła</th>
                  <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Punkty</th>
                  <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-3">
                    <span class="sr-only">Edit</span>
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white">
                <template v-for="(place, index) in groupedRanking" :key="index">
                  <tr class="border-t border-gray-200">
                    <th colspan="5" scope="colgroup" class="bg-gray-50 py-2 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-3">Miejsce {{ index + 1 }}</th>
                  </tr>
                  <tr v-for="(ranking, userId) in place" :key="ranking.user.id" :class="[userId === 0 ? 'border-gray-300' : 'border-gray-200', 'border-t']">
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ user.id === ranking.user.id ? user.name : ranking.user.name ?? '---' }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ user.id === ranking.user.id ? user.surname : ranking.user.surname ?? '---' }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ ranking.user.school.name }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ ranking.points }}</td>
                  </tr>
                </template>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
