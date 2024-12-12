<script setup lang="ts">
import QuizPage from '@/components/UserQuiz/QuizPage.vue'
import { converter } from '@/Helpers/Converter'
import { type PageProps } from '@/Types/PageProps'

const props = defineProps<{ quiz: Quiz } & PageProps>()
</script>

<template>
  <div class="w-full bg-primary text-white text-center font-bold mb-4 p-1">
    Wersja demonstracyjna
  </div>

  <QuizPage
    :user-quiz="converter.quiz.adminToUser(props.quiz)"
    :request-close-quiz="{ method: 'get', href: '/admin/quizzes/' }"
  />
</template>
