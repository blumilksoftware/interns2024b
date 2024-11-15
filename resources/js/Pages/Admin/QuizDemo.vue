<script setup lang="ts">
import Quiz from '@/Pages/User/Quiz.vue'
import { converter } from '@/Helpers/Converter'

defineProps<{ quiz: Quiz }>()
</script>

<template>
  <Quiz :user-quiz="converter.quiz.adminToUser(quiz)" />
</template>
