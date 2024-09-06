<script setup lang="ts">

import {computed, ref} from 'vue'
import { defineProps } from 'vue'
import type { QuizRankingProps} from '@/Types/Ranking'
import { useForm } from '@inertiajs/vue3'
import FormButton from '@/components/Common/FormButton.vue'

const props = defineProps<QuizRankingProps>()

const form = useForm({})

const quiz = ref(props.quiz)
const rankings = ref(props.rankings)

const sortedRankings = computed(() => {
  return [...rankings.value].sort((a, b) => b.points - a.points)
})
</script>

<template>
  <div>
    <h1>Ranking Quizu: {{ quiz.name }}</h1>
    <div class="flex gap-4">
      <FormButton method="post" :href="`/admin/quizzes/${quiz.id}/ranking/publish`" small preserve-scroll>Publikuj</FormButton>
      <FormButton method="post" :href="`/admin/quizzes/${quiz.id}/ranking/unpublish`" small preserve-scroll>Wycofaj publikacje</FormButton>
    </div>
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
