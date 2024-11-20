<script setup lang="ts">
import Divider from '@/components/Common/Divider.vue'
import Button from '@/components/Common/Button.vue'
import {calcSecondsBetweenDates, secondsToHour, timeToString} from '@/Helpers/Time'
import {computed} from 'vue'
import LinkButton from '@/components/Common/LinkButton.vue'
import {Head} from '@inertiajs/vue3'
import QuestionResult from '@/components/QuizResult/QuestionResult.vue'

const props = defineProps<{ userQuiz: UserQuiz, hasRanking: boolean }>()
const duration = secondsToHour(calcSecondsBetweenDates(props.userQuiz.closedAt, props.userQuiz.createdAt))
const points = computed(() =>
  props.userQuiz.questions.filter(
    question => question.answers.some(answer => question.selected === answer.id && answer.correct),
  ).length,
)
</script>

<template>
  <Head :title="`${userQuiz.title} - Wyniki`" />
  
  <Divider>
    <template #default>
      <h1 class="font-bold text-lg text-primary whitespace-nowrap">
        {{ userQuiz.title }}
      </h1>
    </template>

    <template #right>
      <p class="text-primary font-semibold whitespace-nowrap">
        Twój czas rozwiązania testu: {{ timeToString(duration) }}
      </p>
    </template>
  </Divider>

  <div class="flex flex-col p-5 gap-5 max-w-6xl">
    <div v-if="hasRanking" class="text-primary font-semibold whitespace-nowrap">
      Zdobyte punkty: {{ points }}/{{ userQuiz.questions.length }}
    </div>

    <QuestionResult
      v-for="(question, index) in userQuiz.questions" :key="question.id"
      :index="index"
      :question="question"
      :questions-total="userQuiz.questions.length"
      :has-ranking="hasRanking"
    />

    <div v-if="hasRanking" class="h-80 flex flex-col items-center justify-center">
      <p class="font-semibold text-primary text-xl p-5 text-center">To już wszystkie pytania</p>
      <LinkButton large :href="`/quizzes/${userQuiz.quiz}/ranking`">Ranking</LinkButton>
    </div>

    <div v-else class="h-80 flex flex-col items-center justify-center">
      <p class="font-semibold text-primary text-xl p-5 text-center">Ranking jest w trakcie przygotowania, wyślemy powiadomienie na Twoją skrzynkę pocztową gdy będzie gotowy!</p>
      <Button large disabled>Ranking</Button>
    </div>
  </div>
</template>
