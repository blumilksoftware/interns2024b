<!-- eslint-disable @typescript-eslint/no-unused-vars -->
<script setup lang="ts">
import { computed, ref } from 'vue'
import axios from 'axios'
import { router, useForm } from '@inertiajs/vue3'
import dayjs from 'dayjs'
import {type Quiz} from '@/Types/Quiz'
import QuestionComponent from './QuestionComponent.vue'
import EditableInput from '@/components/Common/EditableInput.vue'
import ExapnsionToggleDynamicIcon from '@/components/Icons/ExapnsionToggleDynamicIcon.vue'
import CopyIcon from '@/components/Icons/CopyIcon.vue'
import LockIcon from '@/components/Icons/LockIcon.vue'
import TrashIcon from '@/components/Icons/TrashIcon.vue'
import PencilIcon from '../Icons/PencilIcon.vue'
import CheckIcon from '../Icons/CheckIcon.vue'
import DismissIcon from '../Icons/DismissIcon.vue'

const props = defineProps<{quiz:Quiz, isSelected:boolean, showLockedQuizzes:boolean}>()
const emit = defineEmits(['displayToggle'])
const isRequestOngoing = ref<boolean>(false)
const isEditing = ref<boolean>(false)
const quizRef = computed<Quiz>(()=>props.quiz)

function onStartEditing(){ 
  isEditing.value=!isEditing.value
  if (!props.isSelected)
    toggleQuizView()
}

async function request(url : string, method: 'POST' | 'PATCH' | 'DELETE', payload? : object) {
  isRequestOngoing.value = true
  const axiosRequest = {
    'POST':async () => await axios.post(url, payload),
    'PATCH':async () => await axios.post(url, {...payload, _method:'PATCH'}),
    'DELETE':async () => await axios.delete(url, payload),
  }
  payload = payload ?? {}
  payload = {...payload,
    preserveScroll: true,
    onSuccess: () => isRequestOngoing.value = false,
  }
  const inertiaFormRequest = {
    'POST': () => useForm(payload).post(url),
    'PATCH': () => useForm(payload).delete(url),
    'DELETE': () => useForm({...payload, _method:'PATCH'}).post(url),
  }
  const result = inertiaFormRequest[method]()
  return result
}

async function updateQuiz(payload : object) {
  await request(`/admin/quizzes/${props.quiz.id}`, 'PATCH', payload)
}

function formatDatePretty(date?:number):string|undefined{
  return date ? dayjs(date).format('MMM D, YYYY - h:mm').toString() : undefined
}

function formatDateHTML(date?:number):string|undefined{
  return date ? dayjs(date).format('YYYY-MM-DDTHH:mm').toString() : undefined
}

async function addQuestion(quizId:number){
  useForm({ text: 'Nowy test' }).post(`/admin/quizzes/${quizId}/questions`)
}

async function deleteQuiz(quizId: number) {
  request(`/admin/quizzes/${quizId}`, 'DELETE')
}

function copyQuiz(quizId: number) {
  router.post(`/admin/quizzes/${quizId}/clone`)
}

function toggleQuizView() {
  emit('displayToggle', props.quiz)
}
</script>

<template>
  <div v-show="isRequestOngoing" class="fixed inset-0 bg-white/50" />
  <div
    v-if="!(quiz.locked && showLockedQuizzes)"
    tabindex="0"
    class="mt-4 p-5 bg-white/70 rounded-lg items-center"
    data-name="wrapped-list-element"
  >
    <!-- header -->
    <div :class="isSelected ? 'flex justify-between items-center' : 'grid grid-cols-[1fr,1fr,1fr,.8fr] gap-3 items-center'">
      <div class="flex gap-3">
        <button @click="toggleQuizView()"><ExapnsionToggleDynamicIcon :is-expanded="isSelected" /></button>
        <EditableInput v-model="quizRef.name" :is-editing="isEditing && isSelected" type="text" />
      </div>
      <span v-if="!isSelected">Czas rozpoczęcia: <b class="whitespace-nowrap">{{ formatDatePretty(quiz.scheduledAt) ? formatDatePretty(quiz.scheduledAt) : 'brak' }}</b> </span>
      <span v-if="!isSelected">Czas trwania: <b class="whitespace-nowrap">{{ quiz.duration ? quiz.duration + ' minut': "brak" }}</b> </span>
      <div class="flex gap-5 justify-end">
        <button v-if="!isEditing && !quiz.locked" class="bg-primary rounded-lg py-2 px-4 text-white font-bold">Opublikuj</button>
        <button v-if="!quiz.locked && !isEditing" title="Edytuj test" @click="onStartEditing"><PencilIcon /></button>
        <button v-if="!quiz.locked && isEditing" title="Anuluj edytowanie testu" @click="onStartEditing"><DismissIcon /></button>
        <button v-if="!quiz.locked && isEditing" title="Zapisz edytowany test" @click="onStartEditing"><CheckIcon /></button>
        <button v-if="!isEditing" title="Skopiuj test" @click="copyQuiz(quiz.id)"><CopyIcon /></button>
        <div v-if="quiz.locked" title="Ten test jest zablokowany. Nie można go modyfikować ani usunąć"><LockIcon /></div>
        <button v-else title="Usuń test" @click="deleteQuiz(quiz.id)"><TrashIcon /></button>
      </div>
    </div>
    <!-- header/ -->

    <!-- content -->
    <div v-if="isSelected" class="flex mt-8 px-2 gap-8 flex-col">
      <div class="grid grid-cols-[auto,auto] gap-2 w-fit rounded-lg items-center">
        <span>Rozpoczęcie testu:</span>
        <EditableInput type="datetime-local" :is-editing="isEditing" :v-model:value="formatDateHTML(quiz.scheduledAt)" />
        <span>Zakończenie testu:</span>
        <EditableInput type="datetime-local" :is-editing="isEditing" :v-model="quiz.duration" />
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
