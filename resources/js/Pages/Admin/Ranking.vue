<script setup lang="ts">
import {computed, ref} from 'vue'
import type { QuizRankingProps} from '@/Types/Ranking'
import FormButton from '@/components/Common/FormButton.vue'

const props = defineProps<QuizRankingProps>()

const quiz = ref(props.quiz)
const rankings = ref(props.rankings)

const sortedRankings = computed(() => {
  return [...rankings.value].sort((a, b) => b.points - a.points)
})
</script>

<template>
  <div>
    <h1>Ranking Quizu: {{ quiz.title }}</h1>
    <form @submit.prevent="publish">
      <button type="submit">Publikuj</button>
    </form>
    <form @submit.prevent="unpublish">
      <button type="submit">Wycofaj publikacje</button>
    </form>
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
        <tr v-for="(ranking) in sortedRankings" :key="ranking.user.id">
          <td>{{ ranking.user.id }}</td>
          <td>{{ ranking.user.name }}</td>
          <td>{{ ranking.user.surname }}</td>
          <td>{{ ranking.user.school.name }}</td>
          <td>{{ ranking.points }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
