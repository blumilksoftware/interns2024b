<script setup lang="ts">
import ExapnsionToggleDynamicIcon from '../Icons/ExapnsionToggleDynamicIcon.vue'
import TrashIcon from '@/components/Icons/TrashIcon.vue'
import CopyIcon from '@/components/Icons/CopyIcon.vue'
import CheckDynamicIcon from '@/components/Icons/CheckDynamicIcon.vue'

import { computed, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { type Question } from '@/Types/Question'

const props = defineProps<{quizId:number, question:Question}>()
const isAnswerExpanded = ref<boolean>(false)
const hasAnswers = computed(()=>props.question.answers.length > 0)

function deleteQuestion() {
  router.delete(`/admin/questions/${props.question.id}`)
}

function copyQuestion() {
  router.post(`/admin/questions/${props.question.id}/clone/${props.quizId}`)
}

function addAnswer(){
  router.post(`/admin/questions/${props.question.id}/answers`)
}
</script>

<template>
  <div class="rounded-lg border border-primary/30 flex justify-between">
    <div class="flex flex-col gap-4 p-4">
      <div class="flex flex-col gap-1">
        <b class="text-[1.1rem]">Pytanie {{ question.id }}</b>
        <p>{{ question.text }}</p>
      </div>
      <div class="flex gap-4">
        <button class="w-fit border border-primary/30 rounded-lg py-2 px-3 gap-2 flex bg-white/50" @click="addAnswer">
          <b>+ Dodaj odpowiedź</b>
        </button>
        <button v-if="hasAnswers" class="w-fit border border-primary/30 rounded-lg py-2 px-3 gap-2 flex bg-white/50" @click="isAnswerExpanded=!isAnswerExpanded">
          <b>Odpowiedzi</b>
          <ExapnsionToggleDynamicIcon :is-expanded="isAnswerExpanded" />
        </button>
      </div>
      <ol v-if="isAnswerExpanded && hasAnswers" class="flex flex-col gap-4">
        <li v-for="answer in question.answers" :key="answer.id" class="border border-primary/30 rounded-lg p-4 bg-white/50 flex items-center gap-4">
          <CheckDynamicIcon class="size-6" :is-correct="question.correct==answer.id" />
          {{ answer.text }}
        </li>
      </ol>
    </div>
    <div class="px-3 border-l border-primary/30 flex flex-col justify-evenly">
      <button data-name="copy" @click="copyQuestion"><CopyIcon /></button>
      <button data-name="delete" @click="deleteQuestion"><TrashIcon /></button>
    </div>
  </div>
</template>

<style scoped>
ol > li::marker {
  font-weight: bold;
}
</style>
