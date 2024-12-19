<script setup lang="ts">
import UserAnswer from '@/components/UserQuiz/UserAnswer.vue'

defineProps<{ index: number, question: UserQuestion, questionsTotal: number, timeout: boolean }>()
const emit = defineEmits<{ answer: [question: UserQuestion, answerId: number] }>()
</script>

<template>
  <div class="rounded-xl bg-white shadow border flex flex-col justify-between gap-5 p-5 pb-6">
    <div class="flex flex-col gap-3">
      <b class="text-primary text-lg">
        Pytanie: {{ index + 1 }}/{{ questionsTotal }}
      </b>

      <p class="font-medium">
        {{ question.text }}
      </p>
    </div>

    <form class="flex flex-col gap-5">
      <UserAnswer
        v-for="answer in question.answers"
        :key="answer.id"
        :answer="answer"
        :checked="question.selectedAnswer === answer.id"
        :timeout="timeout"
        @change="answerId => emit('answer', question, answerId)"
      />
    </form>
  </div>
</template>
