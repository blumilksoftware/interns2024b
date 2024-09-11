<script setup lang="ts">

import {computed, ref} from 'vue'
import type { QuizRankingProps} from '@/Types/Ranking'

const props = defineProps<QuizRankingProps>()

const quiz = ref(props.quiz)
const rankings = ref(props.rankings)

const sortedRankings = computed(() => {
  return [...rankings.value].sort((a, b) => b.points - a.points)
})
</script>

<template>
  <div>
    <h1>Ranking Quizu: {{ quiz.name }}</h1>
    <table>
      <thead>
        <tr>
          <th>ID Użytkownika</th>
          <th>Szkoła</th>
          <th>Punkty</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(ranking) in sortedRankings" :key="ranking.user.id">
          <td>{{ ranking.user.id }}</td>
          <td>{{ ranking.user.school.name }}</td>
          <td>{{ ranking.points }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
