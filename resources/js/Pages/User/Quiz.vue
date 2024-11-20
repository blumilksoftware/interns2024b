<script setup lang="ts">
import QuizLayout from '@/Layouts/QuizLayout.vue'
import axios from 'axios'
import FormButton from '@/components/Common/FormButton.vue'
import Button from '@/components/Common/Button.vue'
import { ref } from 'vue'

defineProps<{ userQuiz: UserQuiz }>()

const emptyAnswerMessage = ref(false)
const networkErrorMessage = ref(false)

function handleAnswer(question: UserQuestion, selectedAnswer: number) {
  question.selectedAnswer = selectedAnswer

  axios.post(`/questions/${question.id}/${selectedAnswer}`, { _method: 'patch' }).catch(() => {
    networkErrorMessage.value = true
    question.selectedAnswer = undefined
  })
}
</script>

<template>
  <QuizLayout 
    :user-quiz="userQuiz"
    :empty-answer-message="emptyAnswerMessage"
    :network-error-message="networkErrorMessage"
    @answer="handleAnswer"
  >
    <template #submitButton="{ allQuestionsAnswered }">
      <FormButton 
        v-if="allQuestionsAnswered" 
        large 
        :href="`/quizzes/${userQuiz.id}/close`" 
        method="post"
      >
        Oddaj test
      </FormButton>
      <Button 
        v-else 
        large 
        @click="emptyAnswerMessage = true"
      >
        Oddaj test
      </Button>
    </template>

    <template #submitWithoutAllAnswers>
      <FormButton 
        small 
        :href="`/quizzes/${userQuiz.id}/close`" 
        method="post"
      >
        Oddaj mimo to
      </FormButton>
    </template>

    <template #timeoutButton>
      <FormButton 
        large 
        :href="`/quizzes/${userQuiz.id}/result`" 
        method="get"
      >
        Podsumowanie
      </FormButton>
    </template>
  </QuizLayout>
</template>
