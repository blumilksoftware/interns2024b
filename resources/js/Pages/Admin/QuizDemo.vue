<script setup lang="ts">
import QuizLayout from '@/Layouts/QuizLayout.vue'
import { converter } from '@/Helpers/Converter'

const props = defineProps<{ quiz: Quiz }>()
</script>

<template>
  <div class="w-full bg-primary text-white text-center font-bold mb-4 p-1">Wersja demonstracyjna</div>
  <QuizLayout
    :user-quiz="converter.quiz.adminToUser(props.quiz)"
    :request-close-quiz="{ method: 'get', href: '/admin/quizzes/' }"
  />
</template>
