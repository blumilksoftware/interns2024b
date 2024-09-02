<script setup lang="ts">
import {type QuizSubmission} from '@/Types/QuizSubmission'
import {type Quiz} from '@/Types/Quiz'
import {computed} from 'vue'
import dayjs from 'dayjs'
import FormButton from '@/components/Common/FormButton.vue'
import Divider from '@/components/Common/Divider.vue'

const props = defineProps<{
    submissions: QuizSubmission[]
    quizzes: Quiz[]
  }>()

const started = computed(() => props.quizzes.filter(quiz => quiz.state == 'published'))
const scheduled = computed(() => props.quizzes.filter(quiz => quiz.state == 'locked'))
</script>

<template>
  <div class="w-4/5">
    <Divider>
      <h1 class="font-bold text-xl text-primary text-center p-4 whitespace-nowrap">Trwające konkursy</h1>
    </Divider>
    <div v-for="quiz in started" :key="quiz.id" class="rounded-lg bg-white shadow border px-4 py-2 flex items-center justify-between mb-2">
      <div>
        <p class="font-semibold text-primary">{{ quiz.name }}</p>
        <p class="text-xs py-1">{{ dayjs(quiz.scheduledAt).fromNow() }}</p>
      </div>
      <FormButton class="min-w-24" small method="post" :href="`/quizzes/${quiz.id}/start`">Weź udział</FormButton>
    </div>

    <Divider>
      <h1 class="font-bold text-xl text-primary text-center p-4 whitespace-nowrap">Nadchodzące konkursy</h1>
    </Divider>
    <div v-for="quiz in scheduled" :key="quiz.id" class="rounded-lg bg-white shadow border px-4 py-2 flex items-center justify-between mb-2">
      <div>
        <p class="font-semibold text-primary">{{ quiz.name }}</p>
        <p class="text-xs py-1">{{ dayjs(quiz.scheduledAt).fromNow() }}</p>
      </div>
      <FormButton v-if="!quiz.isUserAssigned" class="min-w-24" small method="post" :href="`/quizzes/${quiz.id}/assign`">Zapisz się</FormButton>
      <div v-else class="min-w-24 text-center border border-gray-500 text-gray-500 font-semibold rounded-md text-sm py-2 px-3 cursor-not-allowed">Zapisano</div>
    </div>

    <Divider>
      <h1 class="font-bold text-xl text-primary text-center p-4 whitespace-nowrap">Historia</h1>
    </Divider>
    <div v-for="submission in submissions" :key="submission.id" class="rounded-lg bg-white shadow border px-4 py-2 flex items-center justify-between mb-2">
      <div>
        <p class="font-semibold text-primary">{{ submission.name }}</p>
        <p class="text-xs py-1">{{ dayjs(submission.closedAt).fromNow() }}</p>
      </div>
      <FormButton class="min-w-24" small method="post" :href="`/submissions/${submission.id}/results`">Wyniki</FormButton>
    </div>
  </div>
</template>
