<script setup lang="ts">

import {type QuizSubmission} from '@/Types/QuizSubmission'
import Divider from '@/components/Common/Divider.vue'
import Button from '@/components/Common/Button.vue'
import {calcSecondsBetweenDates, secondsToHour, timeToString} from '@/Helpers/Time'
import {route} from 'ziggy-js'
import {computed} from 'vue'
import LinkButton from '@/components/Common/LinkButton.vue'
import FakeRadio from '@/components/Common/FakeRadio.vue'
import {Head} from "@inertiajs/vue3";

const props = defineProps<{ submission: QuizSubmission, hasRanking: boolean }>()
const duration = secondsToHour(calcSecondsBetweenDates(props.submission.closedAt, props.submission.openedAt))

const points = computed(() =>
  props.submission.answers.filter(
    record => record.answers.some(answer => record.selected === answer.id && answer.correct),
  ).length,
)
</script>

<template>
  <Head>
    <title>{{ submission.name }} - Wyniki</title>
  </Head>

  <div class="w-full p-2 md:w-7/12">
    <Divider>
      <h1 class="font-bold text-xl text-primary text-center px-4 whitespace-nowrap">
        {{ submission.name }}
      </h1>
    </Divider>
    <div class="w-full flex justify-between">
      <div v-if="hasRanking" class="w-full text-left text-sm font-semibold">
        Zdobyte punkty: {{ points }}/{{ submission.answers.length }}
      </div>

      <div class="w-full text-right text-sm font-semibold">
        Czas trwania testu: {{ timeToString(duration) }}
      </div>
    </div>

    <div v-for="(record, index) in submission.answers" :key="record.id" class="rounded-lg bg-white shadow border flex flex-col justify-between px-4 py-2 m-5">
      <div>
        <p class="pt-2 font-semibold text-primary">Pytanie: {{ index + 1 }}/{{ submission.answers.length }}</p>
        <p class="py-2 mt-2">{{ record.question }}</p>
      </div>

      <div class="mb-3 mt-2">
        <div class="flex flex-col gap-2">
          <FakeRadio
            v-for="answer in record.answers"
            :id="answer.id.toString()"
            :key="answer.id"
            type="radio"
            :native="!hasRanking"
            :checked="record.selected == answer.id || answer.correct"
            :mode="!hasRanking ? 'dot' : answer.correct ? 'v' : 'x'"
            :class="`${record.selected == answer.id ? 'font-semibold' : ''}`"
            :fill="!hasRanking ? 'primary' : answer.correct ? 'green' : 'red'"
          >
            {{ answer.text }}
          </FakeRadio>
        </div>
      </div>
    </div>

    <div v-if="hasRanking" class="h-80 flex flex-col items-center justify-center">
      <p class="font-semibold text-primary text-xl p-5 text-center">To już wszystkie pytania</p>
      <LinkButton small :href="route('quizzes.ranking', submission.quiz)">Ranking</LinkButton>
    </div>

    <div v-else class="h-80 flex flex-col items-center justify-center">
      <p class="font-semibold text-primary text-xl p-5 text-center">Ranking  jest w trakcje przygotowywania, wyślemy powiadomienie na twoją skrzynkę pocztową gdy będzie gotowy!</p>
      <Button small disabled>Ranking</Button>
    </div>
  </div>
</template>
