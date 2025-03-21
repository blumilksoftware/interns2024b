<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { useTimer } from '@/Helpers/Timer'
import { useWindowScroll } from '@vueuse/core'
import { Head } from '@inertiajs/vue3'
import { calcSecondsLeftToDate } from '@/Helpers/Time'
import { type Method } from '@inertiajs/core'

import Divider from '@/components/Common/Divider.vue'
import Banner from '@/components/Common/Banner.vue'
import Button from '@/components/Common/Button.vue'
import MessageBox from '@/components/Common/MessageBox.vue'
import UserQuestion from '@/components/UserQuiz/UserQuestion.vue'
import FormButton from '@/components/Common/FormButton.vue'

const props = defineProps<{ userQuiz: UserQuiz, requestCloseQuiz: { method: Method, href: string } }>()
const questions = ref(props.userQuiz.questions)
const emit = defineEmits<{ answer: [question: UserQuestion, selectedAnswer: number] }>()

const allQuestionsAnswered = computed(
  () => questions.value.every(question => !!question.selectedAnswer),
)

const timeout = ref(false)
const timeoutMessage = ref(false)
const timeoutWarningMessage = ref(false)
const emptyAnswerMessage = ref(false)

const scroll = useWindowScroll()
const showDuration = ref(false)
const durationInMilliseconds = calcSecondsLeftToDate(props.userQuiz.closedAt) * 1000
const fiveMinutesInMilliseconds = 300000

watch(scroll.y, v => showDuration.value = v > 150)

if (durationInMilliseconds > fiveMinutesInMilliseconds) {
  const duration = durationInMilliseconds - fiveMinutesInMilliseconds
  if (duration < 2147483647)
    setTimeout(() => timeoutWarningMessage.value = true, durationInMilliseconds - fiveMinutesInMilliseconds)
}

const timeLeft = useTimer(props.userQuiz.closedAt, () => {
  timeout.value = true
  timeoutMessage.value = true
})

function handleAnswer(question: UserQuestion, selectedAnswer: number) {
  question.selectedAnswer = selectedAnswer
  emit('answer', question, selectedAnswer)
}
</script>

<template>
  <Head :title="userQuiz.title" />

  <Banner
    class="bg-white !text-primary border-b font-semibold"
    :message="timeLeft"
    :show="showDuration"
  />

  <Divider>
    <template #default>
      <h1 class="font-bold text-lg text-primary whitespace-nowrap">
        {{ userQuiz.title }}
      </h1>
    </template>

    <template
      v-if="timeLeft"
      #right
    >
      <p class="text-primary font-semibold whitespace-nowrap">
        {{ timeLeft }}
      </p>
    </template>
  </Divider>

  <div class="flex flex-col p-5 gap-5 max-w-6xl">
    <UserQuestion
      v-for="(question, index) in questions"
      :key="question.id"
      :index="index"
      :question="question"
      :questions-total="questions.length"
      :timeout
      @answer="handleAnswer"
    />

    <div
      v-if="!timeout"
      class="h-80 mx-5 flex flex-col gap-8 items-center justify-center"
    >
      <p class="font-semibold text-primary text-xl text-center">
        To już wszystkie pytania. Czy chcesz oddać test?
      </p>

      <FormButton
        v-if="allQuestionsAnswered"
        large
        :href="requestCloseQuiz.href"
        :method="requestCloseQuiz.method"
      >
        Oddaj test
      </FormButton>

      <Button
        v-else
        large
        @click="emptyAnswerMessage = true"
      >
        Oddaj test
      </Button>
    </div>

    <div
      v-else
      class="h-80 mx-5 flex flex-col gap-8 items-center justify-center"
    >
      <p class="font-semibold text-primary text-xl text-center">
        Czas przewidziany na ten test dobiegł końca. <br> Twój test został przesłany do ocenienia
      </p>

      <FormButton
        large
        :href="requestCloseQuiz.href"
        :method="requestCloseQuiz.method"
      >
        Podsumowanie
      </FormButton>
    </div>
  </div>

  <MessageBox
    :open="emptyAnswerMessage"
    @close="emptyAnswerMessage = false"
  >
    <template #title>
      Pytania bez odpowiedzi
    </template>

    <template #message>
      Nie udzielono odpowiedzi na wszystkie pytania, czy na pewno chcesz oddać test?
    </template>

    <template #buttons>
      <Button
        small
        text
        @click="emptyAnswerMessage = false"
      >
        Wróć
      </Button>

      <FormButton
        small
        :href="requestCloseQuiz.href"
        :method="requestCloseQuiz.method"
      >
        Oddaj mimo to
      </FormButton>
    </template>
  </MessageBox>

  <MessageBox
    :open="timeoutMessage"
    @close="timeoutMessage = false"
  >
    <template #title>
      Koniec czasu
    </template>

    <template #message>
      Czas przewidziany na ten test dobiegł końca. Możliwość udzielania dalszych odpowiedzi została zablokowana.
    </template>

    <template #buttons>
      <Button
        small
        @click="timeoutMessage = false"
      >
        Ok
      </Button>
    </template>
  </MessageBox>

  <MessageBox
    :open="timeoutWarningMessage"
    @close="timeoutWarningMessage = false"
  >
    <template #title>
      Zbliża się koniec czasu
    </template>

    <template #message>
      Pozostało 5 minut do końca testu.
    </template>

    <template #buttons>
      <Button
        small
        @click="timeoutWarningMessage = false"
      >
        Ok
      </Button>
    </template>
  </MessageBox>
</template>
