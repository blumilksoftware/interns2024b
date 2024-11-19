<script setup lang="ts">
import AnswerResult from '@/components/QuizResult/AnswerResult.vue'

defineProps<{ index: number, question: UserQuestion, questionsTotal: number, hasRanking: boolean }>()
</script>

<template>
  <div class="rounded-xl bg-white shadow border flex flex-col justify-between gap-5 p-5 pb-6">
    <div class="flex flex-col gap-3">
      <b class="text-primary text-lg">Pytanie: {{ index + 1 }}/{{ questionsTotal }}</b>
      <p class="font-medium">{{ question.text }}</p>
    </div>

    <form class="flex flex-col gap-5">
      <AnswerResult
        v-for="answer in question.answers" :key="answer.id"
        :checked="question.selectedAnswer == answer.id"
        :mode="!true ? undefined : answer.correct ? 'success' : 'error'"
        :bold="answer.correct"
      >
        {{ answer.text }}
      </AnswerResult>
    </form>
  </div>
</template>
