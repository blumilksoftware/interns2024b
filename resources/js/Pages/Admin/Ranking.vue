<script setup lang="ts">

import {computed, ref} from 'vue'
import { defineProps } from 'vue'
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
          <th>Imię</th>
          <th>Nazwisko</th>
          <th>Szkoła</th>
          <th>Punkty</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(ranking) in sortedRankings" :key="ranking.user_id">
          <td>{{ ranking.user_id }}</td>
          <td>{{ ranking.user_name }}</td>
          <td>{{ ranking.user_surname }}</td>
          <td>{{ ranking.school }}</td>
          <td>{{ ranking.points }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
