<script setup lang="ts">
import QuizLayout from '@/Layouts/QuizLayout.vue'
import axios from 'axios'
import { ref } from 'vue'
import Button from '@/components/Common/Button.vue'
import MessageBox from '@/components/Common/MessageBox.vue'

defineProps<{ userQuiz: UserQuiz }>()
const networkErrorMessage = ref(false)

function handleAnswer(question: UserQuestion, selectedAnswer: number) {
  axios.post(`/questions/${question.id}/${selectedAnswer}`, { _method: 'patch' }).catch(() => {
    networkErrorMessage.value = true
    question.selectedAnswer = undefined
  })
}
</script>

<template>
  <QuizLayout 
    :user-quiz="userQuiz"
    :request-close-quiz="{ method: 'post', href: `/quizzes/${userQuiz.id}/close` }"
    @answer="handleAnswer"
  />
  
  <MessageBox :open="networkErrorMessage" @close="networkErrorMessage = false">
    <template #title>Nie udało się wysłać odpowiedzi</template>

    <template #message>Wystąpił problem z wysłaniem Twojej odpowiedzi. Sprawdź swoje połączenie internetowe i spróbuj ponownie.</template>

    <template #buttons>
      <Button small @click="networkErrorMessage = false">Ok</Button>
    </template>
  </MessageBox>
</template>
