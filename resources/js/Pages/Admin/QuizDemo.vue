<script setup lang="ts">
import QuizLayout from '@/Layouts/QuizLayout.vue'
import { converter } from '@/Helpers/Converter'

const props = defineProps<{ quiz: Quiz }>()
</script>

<template>
  <QuizLayout
    :user-quiz="converter.quiz.adminToUser(props.quiz)"
    :request-close-quiz="{ method: 'get', href: '/admin/quizzes/' }"
  />
</template>
