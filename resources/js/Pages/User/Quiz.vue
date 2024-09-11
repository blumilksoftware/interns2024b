<script setup lang="ts">
import {type QuizSubmission} from '@/Types/QuizSubmission'
import Divider from '@/components/Common/Divider.vue'
import {computed, ref, watch} from 'vue'
import axios from 'axios'
import FormButton from '@/components/Common/FormButton.vue'
import Button from '@/components/Common/Button.vue'
import {type AnswerRecord} from '@/Types/AnswerRecord'
import MessageBox from '@/components/Common/MessageBox.vue'
import {Head} from '@inertiajs/vue3'
import {useTimer} from '@/Helpers/Timer'
import { useWindowScroll } from '@vueuse/core'
import { TransitionRoot } from '@headlessui/vue'
import {useMessageBox} from '@/Helpers/MessageBox'
import {calcSecondsLeftToDate} from '@/Helpers/Time'

const props = defineProps<{ submission: QuizSubmission }>()
const answers = ref(props.submission.answers)
const allAnswered = computed((() => answers.value.every(answer => answer.selected != null)))
const timeout = ref(false)
const emptyAnswerMessage = useMessageBox()
const timeoutMessage = useMessageBox()
const timeoutWarningMessage = useMessageBox()
const networkErrorMessage = useMessageBox()

const scroll = useWindowScroll()
const showDuration = ref(false)
const durationInMilliseconds = calcSecondsLeftToDate(props.submission.closedAt) * 1000
const fiveMinutesInMilliseconds = 300000

watch(scroll.y, v => showDuration.value = v > 150)

if (durationInMilliseconds > fiveMinutesInMilliseconds) {
  setTimeout(timeoutWarningMessage.show, durationInMilliseconds - fiveMinutesInMilliseconds)
}

const timeLeft = useTimer(props.submission.closedAt, () => {
  timeout.value = true
  timeoutMessage.show()
})

function handleAnswer(answers: AnswerRecord, selected: number) {
  answers.selected = selected

  axios.post(`/answers/${answers.id}/${selected}`, { _method: 'patch' }).catch(() => {
    networkErrorMessage.show()
    answers.selected = null
  })
}
</script>

<template>
  <Head>
    <title>{{ submission.name }}</title>
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
        {{ submission.name }}
      </h1>
    </Divider>

    <div class="w-full text-sm font-semibold text-right">
      {{ timeLeft }}
    </div>

    <div v-for="(record, index) in answers" :key="record.id" class="rounded-lg bg-white shadow border flex flex-col justify-between px-4 py-2 m-5">
      <div>
        <p class="pt-2 font-semibold text-primary">Pytanie: {{ index + 1 }}/{{ answers.length }}</p>
        <p class="py-2 mt-2">{{ record.question }}</p>
      </div>

      <div class="mb-3 mt-2">
        <form class="flex flex-col gap-2">
          <label
            v-for="answer in record.answers"
            :key="answer.id"
            :class="`${timeout ? 'cursor-not-allowed' : 'cursor-pointer'}`"
            class="flex items-center text-sm text-black"
            :title="timeout ? 'Czas przewidziany na ten test dobiegł końca' : undefined"
          >
            <input
              :id="answer.id"
              type="radio"
              :disabled="timeout"
              :checked="record.selected === answer.id"
              :class="[timeout ? 'cursor-not-allowed' : 'cursor-pointer']"
              class="mr-2 size-6 border-black text-primary accent-primary"
              @change.prevent="handleAnswer(record, answer.id)"
            >
            {{ answer.text }}
          </label>
        </form>
      </div>
    </div>

    <div v-if="!timeout" class="h-80 flex flex-col items-center justify-center">
      <p class="font-semibold text-primary text-xl p-5 text-center">To już wszystkie pytania. Czy chcesz oddać test?</p>
      <FormButton v-if="allAnswered" small :href="`/submissions/${submission.id}/close`" method="post">Oddaj test</FormButton>
      <Button v-else small @click="emptyAnswerMessage.show">Oddaj test</Button>
    </div>

    <div v-else class="h-80 flex flex-col items-center justify-center">
      <p class="font-semibold text-primary text-xl p-5 text-center">Czas przewidziany na ten test dobiegł końca. <br> Twój test został przesłany do ocenienia</p>
      <FormButton small :href="`/submissions/${submission.id}/result`" method="get">Podsumowanie</FormButton>
    </div>
  </div>

  <MessageBox v-bind="emptyAnswerMessage">
    <template #title>
      Pytania bez odpowiedzi
    </template>

    <template #message>
      Nie udzielono odpowiedzi na wszystkie pytania, czy na pewno chcesz oddać test?
    </template>

    <template #buttons>
      <Button small text @click="emptyAnswerMessage.close">Wróć</Button>
      <FormButton small :href="`/submissions/${submission.id}/close`" method="post">Oddaj mimo to</FormButton>
    </template>
  </MessageBox>

  <MessageBox v-bind="timeoutMessage">
    <template #title>
      Koniec czasu
    </template>

    <template #message>
      Czas przewidziany na ten test dobiegł końca. Możliwość udzielania dalszych odpowiedzi została zablokowana.
    </template>

    <template #buttons>
      <Button small @click="timeoutMessage.close">Ok</Button>
    </template>
  </MessageBox>

  <MessageBox v-bind="networkErrorMessage">
    <template #title>
      Nie udało się wysłać odpowiedzi
    </template>

    <template #message>
      Wystąpił problem z wysłaniem Twojej odpowiedzi. Sprawdź swoje połączenie internetowe i spróbuj ponownie.
    </template>

    <template #buttons>
      <Button small @click="networkErrorMessage.close">Ok</Button>
    </template>
  </MessageBox>

  <MessageBox v-bind="timeoutWarningMessage">
    <template #title>
      Zbliża się koniec czasu
    </template>

    <template #message>
      Pozostało 5 minut do końca testu.
    </template>

    <template #buttons>
      <Button small @click="timeoutWarningMessage.close">Ok</Button>
    </template>
  </MessageBox>
</template>
