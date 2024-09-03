<!-- eslint-disable @typescript-eslint/no-unused-vars -->
<script setup lang="ts">
import { ref } from 'vue'
import dayjs from 'dayjs'
import {type Quiz} from '@/Types/Quiz'
import { type CleanQuestion } from '@/Types/CleanQuestion'
import { type CleanQuiz } from '@/Types/CleanQuiz'
import QuestionComponent from './QuestionComponent.vue'
import EditableInput from '@/components/Common/EditableInput.vue'
import ExapnsionToggleDynamicIcon from '@/components/Icons/ExapnsionToggleDynamicIcon.vue'
import CopyIcon from '@/components/Icons/CopyIcon.vue'
import LockIcon from '@/components/Icons/LockIcon.vue'
import TrashIcon from '@/components/Icons/TrashIcon.vue'
import PencilIcon from '../Icons/PencilIcon.vue'
import CheckIcon from '../Icons/CheckIcon.vue'
import DismissIcon from '../Icons/DismissIcon.vue'
import { Request } from '@/scripts/request'

const props = defineProps<{quiz:Quiz, isSelected:boolean, showLockedQuizzes:boolean}>()
const emit = defineEmits(['displayToggle'])
const isEditing = ref<boolean>(false)
const quizRef = ref<CleanQuiz>(props.quiz)

// editing
function startEditing(){
  isEditing.value=true
  if (!props.isSelected)
    toggleQuizView()
}
function dismissEditing(){
  isEditing.value=false
  quizRef.value = props.quiz
}
function saveEditing(){
  isEditing.value=false
  updateQuiz()
}

// date formatters
function formatDatePretty(date?:number):string|undefined{
  return date ? dayjs(date).format('MMM D, YYYY - h:mm').toString() : undefined
}
function formatDateStandard(date?:number):string|undefined{
  return date ? dayjs(date).format('Y-m-d H:i:s').toString() : undefined
}

function addQuestion(){
  let newId = 0 
  for (const q of quizRef.value.questions)
    if (newId < q.id)
      newId = q.id
  const newQuestion: CleanQuestion = { 
    id: newId+1,
    text: 'New question',
    answers: [],
  }
  quizRef.value.questions.push(newQuestion)
}


// quiz CRUD operations
const request = new Request()

function deleteQuiz() {
  request.sendRequest(`/admin/quizzes/${props.quiz.id}`, 'DELETE')
}
function copyQuiz() {
  request.sendRequest(`/admin/quizzes/${props.quiz.id}/clone`, 'POST')
}
function updateQuiz() {
  request.sendRequest(`/admin/quizzes/${props.quiz.id}`, 'PATCH', quizRef.value)
}
function publish(){
  request.sendRequest(`/admin/quizzes/${props.quiz.id}/publish`, 'POST', quizRef.value)
}

// emits
function toggleQuizView() {
  emit('displayToggle', props.quiz)
}

// state
function isPublished() {
  return props.quiz.state === 'published'
}
function isDraft() {
  return props.quiz.state === 'unlocked'
}
function isScheduled() {
  return props.quiz.state === 'locked'
}
</script>

<template>
  <div v-if="request.isRequestOngoing.value" class="fixed inset-0 bg-white/50">
    <div role="status">
      <svg aria-hidden="true" class="size-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor" />
        <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill" />
      </svg>
      <span class="sr-only">Loading...</span>
    </div>
  </div>
  <div
    v-if="!(isPublished() && showLockedQuizzes)"
    tabindex="0"
    class="mt-4 p-5 bg-white/70 rounded-lg items-center"
    data-name="wrapped-list-element"
  >
    <!-- header -->
    <div class="min-h-12" :class="isSelected ? 'flex justify-between items-center' : 'grid grid-cols-[1fr,1fr,1fr,.8fr] gap-3 items-center'">
      <div class="flex gap-3">
        <button @click="toggleQuizView()"><ExapnsionToggleDynamicIcon :is-expanded="isSelected" /></button>
        <EditableInput v-model="quizRef.name" :is-editing="isEditing && isSelected" type="text" />
      </div>
      <span v-if="!isSelected">Czas rozpoczęcia: <b class="whitespace-nowrap">{{ formatDatePretty(quiz.scheduledAt) ? formatDatePretty(quiz.scheduledAt) : 'brak' }}</b> </span>
      <span v-if="!isSelected">Czas trwania: <b class="whitespace-nowrap">{{ quiz.duration ? quiz.duration + ' minut': "brak" }}</b> </span>
      <div class="flex gap-5 justify-end">
        <button v-if="isDraft() && !isEditing" title="Edytuj test" @click="startEditing"><PencilIcon /></button>
        <button v-if="isEditing" title="Anuluj edytowanie testu" @click="dismissEditing"><DismissIcon /></button>
        <button v-if="isEditing" title="Zapisz edytowany test" @click="saveEditing"><CheckIcon /></button>
        <button v-if="!isEditing" title="Skopiuj test" @click="copyQuiz()"><CopyIcon /></button>
        <div v-if="isPublished()" title="Ten test jest zablokowany. Nie można go modyfikować ani usunąć"><LockIcon /></div>
        <button v-else title="Usuń test" @click="deleteQuiz()"><TrashIcon /></button>
        <button v-if="!isEditing && isDraft()" class="bg-primary rounded-lg py-2 px-4 text-white font-bold" @click="publish">Opublikuj</button>
      </div>
    </div>
    <!-- header/ -->

    <!-- content -->
    <div v-if="isSelected" class="flex mt-8 px-2 gap-8 flex-col">
      <div class="grid grid-cols-[auto,auto] gap-2 w-fit rounded-lg items-center">
        <span>Rozpoczęcie testu:</span>
        <EditableInput type="datetime-local" :is-editing="isEditing" :v-model:value="formatDateStandard(quiz.scheduledAt)" />
        <span>Czas trwania testu:</span>
        <EditableInput placeholder="Podaj czas w minutach" type="number" min="0" :is-editing="isEditing" :v-model="quiz.duration" />
      </div>
          
      <!-- question -->
      <div class="flex flex-col gap-4">
        <div class="flex justify-between">
          <button class="py-2 px-3 rounded-lg border border-primary/30 font-bold bg-white/50" @click="addQuestion()">+ Dodaj pytanie</button>
        </div>

        <data v-if="isSelected" class="flex flex-col gap-4">
          <div v-for="(question, idx) of quizRef.questions" :key="question.id">
            <QuestionComponent v-model="quizRef.questions[idx]" :is-editing="isEditing" :quiz-id="quiz.id" />
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
