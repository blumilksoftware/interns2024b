<script setup lang="ts">

import {type QuizSubmission} from '@/Types/QuizSubmission'
import {type Quiz} from '@/Types/Quiz'
import {Head} from '@inertiajs/vue3'
import {computed} from 'vue'
import dayjs from 'dayjs'
import FormButton from '@/components/Common/FormButton.vue'
import Divider from '@/components/Common/Divider.vue'
import LinkButton from '@/components/Common/LinkButton.vue'

const props = defineProps<{
  submissions: QuizSubmission[]
  quizzes: Quiz[]
}>()

const isClosed = (quiz: Quiz) => dayjs(quiz.scheduledAt).add(quiz.duration ?? 0, 'm').isBefore(Date.now())

const started = computed(() => props.quizzes.filter(quiz => quiz.state == 'published' && !isClosed(quiz)))
const scheduled = computed(() => props.quizzes.filter(quiz => quiz.state == 'locked'))
const history = computed(() => props.submissions.filter(submission => submission.closed))
</script>

<template>
  <Head>
    <title>Konkursy</title>
  </Head>

  <div class="w-full p-2 md:w-4/5">
    <Divider v-if="started.length > 0">
      <h1 class="font-bold text-xl text-primary text-center p-4 whitespace-nowrap">Trwające konkursy</h1>
    </Divider>
    <div v-for="quiz in started" :key="quiz.id" class="rounded-lg bg-white shadow border px-4 py-2 flex items-center justify-between mb-2">
      <div>
        <p class="font-semibold text-sm 2xs:text-base text-primary">{{ quiz.name }}</p>
        <p class="text-xs py-1">{{ dayjs(quiz.scheduledAt).fromNow() }}</p>
      </div>
      <FormButton class="min-w-24 text-center" small method="post" :href="`/quizzes/${quiz.id}/start`" :options="{ preserveScroll: true }">
        Weź udział
      </FormButton>
    </div>

    <Divider v-if="scheduled.length > 0 || started.length == 0">
      <h1 class="font-bold text-xl text-primary text-center p-4 whitespace-nowrap">Nadchodzące konkursy</h1>
    </Divider>
    <div v-for="quiz in scheduled" :key="quiz.id" class="rounded-lg bg-white shadow border px-4 py-2 flex items-center justify-between mb-2">
      <div>
        <p class="font-semibold text-sm 2xs:text-base text-primary">{{ quiz.name }}</p>
        <p class="text-xs py-1">{{ dayjs(quiz.scheduledAt).fromNow() }}</p>
      </div>

      <FormButton v-if="!quiz.isUserAssigned" class="min-w-24 text-center" small method="post" :href="`/quizzes/${quiz.id}/assign`" :options="{ preserveScroll: true }">
        Zapisz się
      </FormButton>
      <FormButton v-else class="min-w-24 text-center" disabled small method="post" :href="`/quizzes/${quiz.id}/assign`">
        Zapisano
      </FormButton>
    </div>

    <div v-if="scheduled.length == 0 && started.length == 0">
      <p>Obecnie nie mamy zaplanowanych konkursów.</p>
    </div>

    <Divider v-if="history.length > 0">
      <h1 class="font-bold text-xl text-primary text-center p-4 whitespace-nowrap">Historia</h1>
    </Divider>
    <div v-for="submission in history" :key="submission.id" class="rounded-lg bg-white shadow border px-4 py-2 flex items-center justify-between mb-2">
      <div>
        <p class="font-semibold text-sm 2xs:text-base text-primary">{{ submission.name }}</p>
        <p class="text-xs py-1">{{ dayjs(submission.closedAt).fromNow() }}</p>
      </div>
      <LinkButton class="min-w-24 text-center" small :href="`/quizzes/${submission.id}/result`">
        Wyniki
      </LinkButton>
    </div>
  </div>
</template>
