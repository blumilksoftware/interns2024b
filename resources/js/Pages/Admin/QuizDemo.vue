<script setup lang="ts">
import QuizLayout from '@/Layouts/QuizLayout.vue'
import { converter } from '@/Helpers/Converter'

defineProps<{ quiz: Quiz }>()
</script>

<template>
  <QuizLayout :user-quiz="converter.quiz.adminToUser(quiz)" />
</template>
