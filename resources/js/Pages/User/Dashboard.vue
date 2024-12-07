<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { computed } from 'vue'
import dayjs from 'dayjs'
import FormButton from '@/components/Common/FormButton.vue'
import Divider from '@/components/Common/Divider.vue'
import LinkButton from '@/components/Common/LinkButton.vue'
import QuizItem from '@/components/Dashboard/QuizItem.vue'
import { type PageProps } from '@/Types/PageProps'

const props = defineProps<{
  userQuizzes: UserQuiz[]
  quizzes: Quiz[]
} & PageProps>()

const isClosed = (quiz: Quiz) => (
  dayjs(quiz.scheduledAt).add(quiz.duration ?? 0, 'm').isBefore(Date.now()) ||
    props.userQuizzes.find(userQuiz => userQuiz.quiz === quiz.id)?.closed
)

const started = computed(() => props.quizzes.filter(quiz => quiz.state == 'published' && !isClosed(quiz)))
const scheduled = computed(() => props.quizzes.filter(quiz => quiz.state == 'locked'))
const history = computed(() => props.userQuizzes.filter(userQuiz => userQuiz.closed))
</script>

<template>
  <Head title="Konkursy" />

  <div class="flex flex-col w-full p-5 gap-5 max-w-6xl">
    <Divider v-if="started.length > 0">
      <h1 class="font-bold text-lg text-primary whitespace-nowrap">Trwające konkursy</h1>
    </Divider>
    <QuizItem
      v-for="quiz in started"
      :key="quiz.id"
      :title="quiz.title"
      :time="quiz.scheduledAt"
    >
      <FormButton button-class="min-w-32 w-full 2xs:w-fit" class="w-full 2xs:w-fit" method="post" :href="`/quizzes/${quiz.id}/start`" preserve-scroll>
        Weź udział
      </FormButton>
    </QuizItem>

    <Divider v-if="scheduled.length > 0 || started.length == 0">
      <h1 class="font-bold text-lg text-primary whitespace-nowrap">Nadchodzące konkursy</h1>
    </Divider>
    <QuizItem
      v-for="quiz in scheduled"
      :key="quiz.id"
      :title="quiz.title"
      :time="quiz.scheduledAt"
    >
      <FormButton v-if="!quiz.isUserAssigned" button-class="min-w-32 w-full 2xs:w-fit" class="w-full 2xs:w-fit" method="post" :href="`/quizzes/${quiz.id}/assign`" preserve-scroll>
        Zapisz się
      </FormButton>

      <FormButton v-else button-class="min-w-32 w-full 2xs:w-fit" class="w-full 2xs:w-fit" disabled method="post" :href="`/quizzes/${quiz.id}/assign`">
        Zapisano
      </FormButton>
    </QuizItem>

    <div v-if="scheduled.length == 0 && started.length == 0">
      <p>Obecnie nie mamy zaplanowanych żadnych konkursów.</p>
    </div>

    <Divider v-if="history.length > 0">
      <h1 class="font-bold text-lg text-primary whitespace-nowrap">Historia</h1>
    </Divider>
    <QuizItem
      v-for="userQuiz in history"
      :key="userQuiz.id"
      :title="userQuiz.title"
      :time="userQuiz.closedAt"
    >
      <LinkButton class="min-w-32 w-full 2xs:w-fit" button-class="justify-center" :href="`/quizzes/${userQuiz.id}/result`">
        Wyniki
      </LinkButton>
    </QuizItem>
  </div>
</template>
