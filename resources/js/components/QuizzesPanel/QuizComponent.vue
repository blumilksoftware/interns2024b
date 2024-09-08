<script setup lang="ts">
import { computed, onBeforeUnmount, ref } from 'vue'
import dayjs, { type ConfigType } from 'dayjs'
import { type Quiz } from '@/Types/Quiz'
import { type CleanQuestion } from '@/Types/CleanQuestion'
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
import { type VisitPayload } from '@/Types/VisitPayload'
import { nanoid } from 'nanoid'
import Datepicker from '@vuepic/vue-datepicker'
import '@vuepic/vue-datepicker/dist/main.css'
import CalendarIcon from '../Icons/CalendarIcon.vue'

const props = defineProps<{quiz:Quiz, isSelected:boolean, showLockedQuizzes:boolean}>()
const emit = defineEmits(['displayToggle'])
const isEditing = ref(false)
const quizRef = ref<Quiz>(props.quiz)
quizRef.value.scheduledAt = formatDateHTML(quizRef.value.scheduledAt)
const currentTime = ref(Date.now())
const updateTimeInterval = setInterval(()=>currentTime.value = Date.now(), 1000)
onBeforeUnmount(()=>{clearInterval(updateTimeInterval)})

const isReadyToSchedule = computed(()=>{
  if (quizRef.value.scheduledAt && quizRef.value.duration)
    return Date.parse(quizRef.value.scheduledAt) > currentTime.value
  return false
})


// editing
function edit(){
  isEditing.value=true
  if (!props.isSelected)
    toggleQuizView()
}
function dismissEditing(){
  quizRef.value = {...props.quiz}
  isEditing.value=false
}

// date formatters
function formatDatePretty(date?: ConfigType) {
  return date ? dayjs(date).format('DD.MM.YYYY HH:mm') : 'brak'
}

function formatDateHTML(date: ConfigType) {
  return date ? dayjs(date).format('YYYY-MM-DDTHH:mm') : ''
}

function addQuestion(){
  const newQuestion: CleanQuestion = { 
    key: nanoid(),
    text: 'Nowe pytanie',
    answers: [],
  }
  quizRef.value.questions.push(newQuestion)
}


// quiz CRUD operations
const request = new Request()

function deleteQuiz() {
  request.sendRequest(`/admin/quizzes/${quizRef.value.id}`, {method: 'delete'})
}
function copyQuiz() {
  request.sendRequest(`/admin/quizzes/${quizRef.value.id}/clone`, {method: 'post'})
}
function updateQuiz() {
  const payload : VisitPayload = {
    method: 'patch',
    data: quizRef.value,
    onSuccess: ()=>isEditing.value = false,
  }
  request.sendRequest(`/admin/quizzes/${quizRef.value.id}`, payload)
}
function schedule() {
  if (!isReadyToSchedule.value) return
  request.sendRequest(`/admin/quizzes/${quizRef.value.id}/lock`,{method:'post'})
}
function unSchedule() {
  request.sendRequest(`/admin/quizzes/${quizRef.value.id}/unlock`,{method:'post'})
}
function deleteQuestion(question:CleanQuestion){
  quizRef.value.questions = quizRef.value.questions.filter((q:CleanQuestion) => q !== question)
}

// emits
function toggleQuizView() {
  emit('displayToggle', quizRef.value)
}

// state
function isPublished() {
  return quizRef.value.state === 'published'
}
function isDraft() {
  return quizRef.value.state === 'unlocked'
}
function isScheduled() {
  return quizRef.value.state === 'locked'
}
</script>

<template>
  <div
    v-if="!(isPublished() && showLockedQuizzes)"
    tabindex="0"
    class="mt-4 p-5 bg-white/70 rounded-lg items-center relative shadow"
  >
    <div v-if="request.isRequestOngoing.value" class="absolute bg-white/50 backdrop-blur-md z-10 size-full left-0 flex items-center justify-center -mt-5">
      <div
        class="inline-block size-8 animate-spin rounded-full border-4 border-solid border-current border-e-transparent align-[-0.125em] text-primary motion-reduce:animate-[spin_1.5s_linear_infinite]"
        role="status"
      />
    </div>

    <!-- header -->
    <div class="min-h-12" :class="isSelected ? 'flex justify-between items-center' : 'grid grid-cols-[1fr,1fr,1fr,.8fr] gap-3 items-center'">
      <div class="flex gap-3">
        <button @click="toggleQuizView()"><ExapnsionToggleDynamicIcon :is-expanded="isSelected" /></button>
        <EditableInput v-model="quizRef.title" :error="request.errors.value.title" :is-editing="isEditing && isSelected" type="text" />
      </div>
      <span v-if="!isSelected">Rozpoczęcie testu: <b class="whitespace-nowrap">{{ formatDatePretty(quiz.scheduledAt) }}</b> </span>
      <span v-if="!isSelected">Czas trwania testu: <b class="whitespace-nowrap">{{ quiz.duration ? quiz.duration + ' min': "brak" }}</b> </span>
      <div class="flex gap-5 justify-end">
        <button v-if="!isEditing && isDraft()" :title="!isReadyToSchedule ? 'Test jest niekompletny, lub czas jest źle ustawiony' : 'Udostępnij test uczniom'" :disabled="!isReadyToSchedule" class="disabled:opacity-50 bg-primary rounded-lg py-2 px-4 text-white font-bold" @click="schedule">Opublikuj</button>
        <button v-if="isScheduled()" class="border border-primary rounded-lg py-2 px-4 text-primary font-bold" @click="unSchedule">Wycofaj</button>
        <button v-if="isDraft() && !isEditing" title="Edytuj test" @click="edit"><PencilIcon /></button>
        <button v-if="isEditing" title="Anuluj edytowanie testu" @click="dismissEditing"><DismissIcon /></button>
        <button v-if="isEditing" title="Zapisz edytowany test" @click="updateQuiz"><CheckIcon /></button>
        <button v-if="!isEditing" title="Skopiuj test" @click="copyQuiz()"><CopyIcon /></button>
        <div v-if="isPublished()" title="Ten test jest zablokowany. Nie można go modyfikować ani usunąć"><LockIcon /></div>
        <button v-else title="Usuń test" @click="deleteQuiz()"><TrashIcon /></button>
      </div>
    </div>
    <!-- header/ -->

    <!-- content -->
    <div v-if="isSelected" class="flex mt-8 px-2 gap-8 flex-col">
      <div class="grid grid-cols-[auto,auto] gap-2 w-fit rounded-lg items-center">
        <span>Rozpoczęcie testu:</span>
        <b v-if="!isEditing">{{ formatDatePretty(quizRef.scheduledAt) }}</b>
        <Datepicker
          v-else
          v-model="quizRef.scheduledAt"
          locale="pl" 
          :format="formatDatePretty"
          :ui="{
            menu: 'datepicker-menu',
            input:'datepicker',
          }"
        >
          <template #input-icon>
            <CalendarIcon />
          </template>
        </Datepicker>
        
        <span>Czas trwania testu <span v-if="isEditing">(min)</span>:</span>
        <EditableInput v-model="quizRef.duration" type="number" min="0" :is-editing="isEditing" />
      </div>
          
      <!-- question -->
      <div class="flex flex-col gap-4">
        <div v-if="isEditing" class="flex justify-between">
          <button class="py-2 px-3 rounded-lg border border-primary/30 font-bold bg-white/50" @click="addQuestion()">+ Dodaj pytanie</button>
        </div>

        <data v-if="isSelected" class="flex flex-col gap-4">
          <div v-for="(question, idx) of quiz.questions" :key="question.key">
            <QuestionComponent 
              v-model="quizRef.questions[idx]" :is-editing="isEditing"
              :index="idx" :questions-length="quiz.questions.length" @delete="deleteQuestion"
            />
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
