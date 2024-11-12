<script setup lang="ts">
import Divider from '@/components/Common/Divider.vue'
import {computed, ref, watch} from 'vue'
import axios from 'axios'
import FormButton from '@/components/Common/FormButton.vue'
import Button from '@/components/Common/Button.vue'
import MessageBox from '@/components/Common/MessageBox.vue'
import {Head} from '@inertiajs/vue3'
import {useTimer} from '@/Helpers/Timer'
import { useWindowScroll } from '@vueuse/core'
import { TransitionRoot } from '@headlessui/vue'
import {calcSecondsLeftToDate} from '@/Helpers/Time'

const props = defineProps<{ userQuiz: UserQuiz }>()

const questions = ref(props.userQuiz.questions)
const allQuestionsAnswered = computed(
  () => questions.value.every(question => question.selectedAnswer !== undefined),
)
const timeout = ref(false)
const emptyAnswerMessage = ref(false)
const timeoutMessage = ref(false)
const timeoutWarningMessage = ref(false)
const networkErrorMessage = ref(false)

const scroll = useWindowScroll()
const showDuration = ref(false)
const durationInMilliseconds = calcSecondsLeftToDate(props.userQuiz.closedAt) * 1000
const fiveMinutesInMilliseconds = 300000

watch(scroll.y, v => showDuration.value = v > 150)

if (durationInMilliseconds > fiveMinutesInMilliseconds) {
  setTimeout(() => timeoutWarningMessage.value = true, durationInMilliseconds - fiveMinutesInMilliseconds)
}

const timeLeft = useTimer(props.userQuiz.closedAt ?? 0, () => {
  timeout.value = true
  timeoutMessage.value = true
})

function handleAnswer(question: UserQuestion, selectedAnswer: number) {
  question.selectedAnswer = selectedAnswer

  axios.post(`/questions/${question.id}/${selectedAnswer}`, { _method: 'patch' }).catch(() => {
    networkErrorMessage.value = true
    question.selectedAnswer = undefined
  })
}
</script>

<template>
  <Head>
    <title>{{ userQuiz.title }}</title>
  </Head>
  <TransitionRoot
    :show="showDuration"
    enter="transition-opacity duration-75"
    enter-from="opacity-0"
    enter-to="opacity-100"
    leave="transition-opacity duration-50"
    leave-from="opacity-100"
    leave-to="opacity-0"
    appear
  >
    <div class="fixed top-0 right-0 w-full text-center bg-white border px-6 py-2.5 font-bold text-sm text-black">
      {{ timeLeft }}
    </div>
  </TransitionRoot>

  <div class="w-full p-2 md:max-w-5xl">
    <Divider>
      <h1 class="font-bold text-xl text-primary text-center px-4 whitespace-nowrap">
        {{ userQuiz.title }}
      </h1>
    </Divider>

    <div class="w-full text-sm font-semibold text-right">
      {{ timeLeft }}
    </div>

    <div v-for="(question, index) in questions" :key="question.id" class="rounded-lg bg-white shadow border flex flex-col justify-between px-4 py-2 m-5">
      <div>
        <p class="pt-2 font-semibold text-primary">Pytanie: {{ index + 1 }}/{{ questions.length }}</p>
        <p class="py-2 mt-2">{{ question.text }}</p>
      </div>

      <div class="mb-3 mt-2">
        <form class="flex flex-col gap-2">
          <label
            v-for="answer in question.answers"
            :key="answer.id"
            :class="[timeout ? 'cursor-not-allowed' : 'cursor-pointer']"
            class="flex items-center text-sm text-black"
            :title="timeout ? 'Czas przewidziany na ten test dobiegł końca' : undefined"
          >
            <input
              :id="`${answer.id}`"
              type="radio"
              :disabled="timeout"
              :checked="question.selectedAnswer === answer.id"
              :class="[timeout ? 'cursor-not-allowed' : 'cursor-pointer']"
              class="mr-2 size-6 border-black text-primary accent-primary"
              @change.prevent="handleAnswer(question, answer.id!)"
            >
            {{ answer.text }}
          </label>
        </form>
      </div>
    </div>

    <div v-if="!timeout" class="h-80 flex flex-col items-center justify-center">
      <p class="font-semibold text-primary text-xl p-5 text-center">To już wszystkie pytania. Czy chcesz oddać test?</p>
      <FormButton v-if="allQuestionsAnswered" small :href="`/quizzes/${userQuiz.id}/close`" method="post">Oddaj test</FormButton>
      <Button v-else small @click="emptyAnswerMessage = true">Oddaj test</Button>
    </div>

    <div v-else class="h-80 flex flex-col items-center justify-center">
      <p class="font-semibold text-primary text-xl p-5 text-center">Czas przewidziany na ten test dobiegł końca. <br> Twój test został przesłany do ocenienia</p>
      <FormButton small :href="`/quizzes/${userQuiz.id}/result`" method="get">Podsumowanie</FormButton>
    </div>
  </div>

  <MessageBox :open="emptyAnswerMessage" @close="emptyAnswerMessage = false">
    <template #title>
      Pytania bez odpowiedzi
    </template>

    <template #message>
      Nie udzielono odpowiedzi na wszystkie pytania, czy na pewno chcesz oddać test?
    </template>

    <template #buttons>
      <Button small text @click="emptyAnswerMessage = false">Wróć</Button>
      <FormButton small :href="`/quizzes/${userQuiz.id}/close`" method="post">Oddaj mimo to</FormButton>
    </template>
  </MessageBox>

  <MessageBox :open="timeoutMessage" @close="timeoutMessage = false">
    <template #title>
      Koniec czasu
    </template>

    <template #message>
      Czas przewidziany na ten test dobiegł końca. Możliwość udzielania dalszych odpowiedzi została zablokowana.
    </template>

    <template #buttons>
      <Button small @click="timeoutMessage = false">Ok</Button>
    </template>
  </MessageBox>

  <MessageBox :open="networkErrorMessage" @close="networkErrorMessage = false">
    <template #title>
      Nie udało się wysłać odpowiedzi
    </template>

    <template #message>
      Wystąpił problem z wysłaniem Twojej odpowiedzi. Sprawdź swoje połączenie internetowe i spróbuj ponownie.
    </template>

    <template #buttons>
      <Button small @click="networkErrorMessage = false">Ok</Button>
    </template>
  </MessageBox>

  <MessageBox :open="timeoutWarningMessage" @close="timeoutWarningMessage = false">
    <template #title>
      Zbliża się koniec czasu
    </template>

    <template #message>
      Pozostało 5 minut do końca testu.
    </template>

    <template #buttons>
      <Button small @click="timeoutWarningMessage = false">Ok</Button>
    </template>
  </MessageBox>
</template>
