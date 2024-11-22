<script setup lang="ts">
import CautionBanner from '@/components/Common/CautionBanner.vue'
import QuizLayout from '@/Layouts/QuizLayout.vue'
import { converter } from '@/Helpers/Converter'

const props = defineProps<{ quiz: Quiz }>()
</script>

<template>
  <CautionBanner class="mb-5" text="To jest wersja demonstracyjna strony testu" />
  <QuizLayout
    :user-quiz="converter.quiz.adminToUser(props.quiz)"
    :request-close-quiz="{ method: 'get', href: '/admin/quizzes/' }"
  />
</template>
