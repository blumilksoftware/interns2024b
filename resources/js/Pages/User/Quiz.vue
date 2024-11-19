<script setup lang="ts">
import Divider from '@/components/Common/Divider.vue'
import {computed, ref, watch} from 'vue'
import axios from 'axios'
import FormButton from '@/components/Common/FormButton.vue'
import Button from '@/components/Common/Button.vue'
import MessageBox from '@/components/Common/MessageBox.vue'
import UserQuestion from '@/components/UserQuiz/UserQuestion.vue'
import {Head} from '@inertiajs/vue3'
import {useTimer} from '@/Helpers/Timer'
import { useWindowScroll } from '@vueuse/core'
import {calcSecondsLeftToDate} from '@/Helpers/Time'
import Banner from '@/components/Common/Banner.vue'

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
  <Head :title="userQuiz.title" />
  
  <Banner
    v-model="timeLeft"
    class="bg-white !text-primary border-b font-semibold"
    :show="showDuration"
    hide-close-button
  />

  <Divider>
    <template #default>
      <h1 class="font-bold text-lg text-primary whitespace-nowrap">
        {{ userQuiz.title }}
      </h1>
    </template>

    <template #right>
      <p class="text-primary font-semibold whitespace-nowrap">
        {{ timeLeft }}
      </p>
    </template>
  </Divider>

  <div class="flex flex-col p-5 gap-5 max-w-6xl">
    <UserQuestion
      v-for="(question, index) in questions" :key="question.id"
      :index="index"
      :question="question"
      :questions-total="questions.length"
      :timeout
      @answer="handleAnswer"
    />

    <div v-if="!timeout" class="h-80 mx-5 flex flex-col gap-8 items-center justify-center">
      <p class="font-semibold text-primary text-xl text-center">To już wszystkie pytania. Czy chcesz oddać test?</p>
      <FormButton v-if="allQuestionsAnswered" large :href="`/quizzes/${userQuiz.id}/close`" method="post">Oddaj test</FormButton>
      <Button v-else large @click="emptyAnswerMessage = true">Oddaj test</Button>
    </div>

    <div v-else class="h-80 mx-5 flex flex-col gap-8 items-center justify-center">
      <p class="font-semibold text-primary text-xl text-center">Czas przewidziany na ten test dobiegł końca. <br> Twój test został przesłany do ocenienia</p>
      <FormButton large :href="`/quizzes/${userQuiz.id}/result`" method="get">Podsumowanie</FormButton>
    </div>
  </div>

  <MessageBox :open="emptyAnswerMessage" @close="emptyAnswerMessage = false">
    <template #title>Pytania bez odpowiedzi</template>

    <template #message>Nie udzielono odpowiedzi na wszystkie pytania, czy na pewno chcesz oddać test?</template>

    <template #buttons>
      <Button small text @click="emptyAnswerMessage = false">Wróć</Button>
      <FormButton small :href="`/quizzes/${userQuiz.id}/close`" method="post">Oddaj mimo to</FormButton>
    </template>
  </MessageBox>

  <MessageBox :open="timeoutMessage" @close="timeoutMessage = false">
    <template #title>Koniec czasu</template>

    <template #message>Czas przewidziany na ten test dobiegł końca. Możliwość udzielania dalszych odpowiedzi została zablokowana.</template>

    <template #buttons>
      <Button small @click="timeoutMessage = false">Ok</Button>
    </template>
  </MessageBox>

  <MessageBox :open="networkErrorMessage" @close="networkErrorMessage = false">
    <template #title>Nie udało się wysłać odpowiedzi</template>

    <template #message>Wystąpił problem z wysłaniem Twojej odpowiedzi. Sprawdź swoje połączenie internetowe i spróbuj ponownie.</template>

    <template #buttons>
      <Button small @click="networkErrorMessage = false">Ok</Button>
    </template>
  </MessageBox>

  <MessageBox :open="timeoutWarningMessage" @close="timeoutWarningMessage = false">
    <template #title>Zbliża się koniec czasu</template>

    <template #message>Pozostało 5 minut do końca testu.</template>

    <template #buttons>
      <Button small @click="timeoutWarningMessage = false">Ok</Button>
    </template>
  </MessageBox>
</template>
