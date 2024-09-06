<script setup lang="ts">

import {type QuizSubmission} from '@/Types/QuizSubmission'
import Divider from '@/components/Common/Divider.vue'
import {computed, ref} from 'vue'
import axios from 'axios'
import FormButton from '@/components/Common/FormButton.vue'
import Button from '@/components/Common/Button.vue'
import {type AnswerRecord} from '@/Types/AnswerRecord'
import TimeLeft from '@/components/Common/TimeLeft.vue'
import MessageBox, { useMessageBox } from '@/components/Common/MessageBox.vue'
import {Head} from '@inertiajs/vue3'

const props = defineProps<{ submission: QuizSubmission }>()
const answers = ref(props.submission.answers)
const allAnswered = computed((() => answers.value.every(answer => answer.selected != null)))
const timeout = ref(false)
const emptyAnswerMessage = useMessageBox()
const timeoutMessage = useMessageBox()

function handleTimeout() {
  timeout.value = true
  timeoutMessage.show()
}

function handleAnswer(answers: AnswerRecord, selected: number) {
  axios.post(`/answers/${answers.id}/${selected}`, { _method: 'patch' })
  answers.selected = selected
}
</script>

<template>
  <Head>
    <title>{{ submission.name }}</title>
  </Head>

  <div class="w-full p-2 md:w-7/12">
    <Divider>
      <h1 class="font-bold text-xl text-primary text-center px-4 whitespace-nowrap">
        {{ submission.name }}
      </h1>
    </Divider>
    <div class="w-full text-right text-sm font-semibold">
      <TimeLeft :to="submission.closedAt" @timeout="handleTimeout" />
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
              :id="answer.id.toString()"
              type="radio"
              :disabled="timeout"
              :checked="record.selected == answer.id"
              :class="`${timeout ? 'cursor-not-allowed' : 'cursor-pointer'}`"
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
      Uwaga
    </template>

    <template #message>
      Nie udzielono odpowiedzi na wszystkie pytania, czy na pewno chcesz oddać test?
    </template>

    <template #buttons>
      <Button small @click="emptyAnswerMessage.close">Wróć</Button>
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
</template>
