
<script setup lang="ts">
import CopyIcon from '@/components/Icons/CopyIcon.vue'
import LockIcon from '@/components/Icons/LockIcon.vue'
import TrashIcon from '@/components/Icons/TrashIcon.vue'
import ExapnsionToggleDynamicIcon from '@/components/Icons/ExapnsionToggleDynamicIcon.vue'
import EditableInput from '@/components/Common/EditableInput.vue'
import dayjs from 'dayjs'
import {type Quiz} from '@/Types/Quiz'
import { router } from '@inertiajs/vue3'
import QuestionComponent from './QuestionComponent.vue'
import axios from 'axios'

const props = defineProps<{quiz:Quiz, isSelected:boolean, showLockedQuizzes:boolean}>()
const emit = defineEmits(['displayToggle'])

function formatDatePretty(date?:number):string|undefined{
  return date ? dayjs(date).format('MMM D, YYYY - h:mm').toString() : undefined
}
function formatDateHTML(date?:number):string|undefined{
  return date ? dayjs(date).format('YYYY-MM-DDTHH:mm').toString() : undefined
}

function toggleQuizView(quiz:Quiz) {
  emit('displayToggle', quiz)
}
function objectToForm(data: any) {
  const form = new FormData()
  Object.keys(data).forEach(key => form.append(key, data[key] ))
  return form
}
function addQuestion(quizId:number){
  router.post(`/admin/quizzes/${quizId}/questions`,  {
    text: 'Nowy test',
  })
}
function deleteQuiz(quizId: number) {
  router.delete(`/admin/quizzes/${quizId}`)
}
function copyQuiz(quizId: number) {
  router.post(`/admin/quizzes/${quizId}/clone`)
}
async function updateQuiz(payload : any) {
  const str = `/admin/quizzes/${props.quiz.id}`
  await axios.post(str,{method:'patch', data:objectToForm(payload)})
}

function updateTitle(value:string) {
  updateQuiz({...props.quiz, name:value})
}
</script>

<template>
  <div
    v-if="!(quiz.locked && showLockedQuizzes)"
    tabindex="0"
    class="mt-4 p-5 bg-white/70 rounded-lg items-center"
    data-name="wrapped-list-element"
  >
    <!-- header -->
    <div :class="isSelected ? 'flex justify-between' : 'grid grid-cols-[1fr,1fr,1fr,auto] gap-3'">
      <div class="flex gap-3">
        <button @click="toggleQuizView(quiz)">
          <ExapnsionToggleDynamicIcon :is-expanded="isSelected" />
        </button>
        <b v-if="!isSelected">{{ quiz.name }}</b>
        <EditableInput v-else :icon="true" type="text" :bold="true" :value="quiz.name" @update-value="updateTitle" />
      </div>
      <span v-if="!isSelected">Czas rozpoczęcia:
        <b class="whitespace-nowrap">{{ formatDatePretty(quiz.scheduledAt) ? formatDatePretty(quiz.scheduledAt) : 'brak' }}</b>
      </span>
      <span v-if="!isSelected">Czas trwania:
        <b class="whitespace-nowrap">{{ quiz.scheduledUntil ? quiz.scheduledUntil + ' minut': "brak" }}</b>
      </span>

      <div class="flex gap-5 justify-end">
        <button
          v-if="isSelected"
          class="bg-primary rounded-lg py-2 px-4 text-white font-bold"
        >
          Publish
        </button>
        <button class="flex items-center h-full" @click="copyQuiz(quiz.id)"> <CopyIcon /> </button>
        <div v-if="quiz.locked" class="flex items-center h-full"> <LockIcon /> </div>
        <button v-else class="flex items-center h-full" @click="deleteQuiz(quiz.id)"> <TrashIcon /> </button>
      </div>
    </div>
    <!-- header/ -->

    <!-- content -->
    <div v-if="isSelected" class="flex mt-8 px-2 gap-8 flex-col">
      <div class="grid grid-cols-[auto,auto] gap-2 w-fit rounded-lg items-center">
        <span>Rozpoczęcie testu:</span>
        <EditableInput type="datetime-local" :value="formatDateHTML(quiz.scheduledAt)" />
        <span>Zakończenie testu:</span>
        <EditableInput type="datetime-local" :value="formatDateHTML(quiz.scheduledUntil)" />
      </div>
          
      <!-- question -->
      <div class="flex flex-col gap-4">
        <div class="flex justify-between">
          <button class="py-2 px-3 rounded-lg border border-primary/30 font-bold bg-white/50" @click="addQuestion(quiz.id)">+ Dodaj pytanie</button>
        </div>

        <data v-if="isSelected" class="flex flex-col gap-4">
          <div v-for="question of quiz.questions" :key="question.id">
            <QuestionComponent :quiz-id="quiz.id" :question="question" />
          </div>
        </data>
      </div>
      <!-- question/ -->
    </div>
    <!-- content/ -->
  </div>
  <footer v-if="isSelected" class="flex justify-end mt-4 px-4 gap-4">
    <span class="text-accent-600 text-[.95rem]"> Stworzony: {{ formatDatePretty(quiz.createdAt) }}</span>
    <span class="text-accent-300 text-[.95rem]"> |</span>
    <span class="text-accent-600 text-[.95rem]"> Ostatnio edytowany: {{ formatDatePretty(quiz.updatedAt) }}</span>
  </footer>
</template>
