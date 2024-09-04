<script setup lang="ts">
import { ref, provide, watch } from 'vue'
import SortIcon from '@/components/Icons/SortIcon.vue'
import EyeDynamicIcon from '@/components/Icons/EyeDynamicIcon.vue'
import FilterIcon from '@/components/Icons/FilterIcon.vue'
import QuizComponent from '@/components/QuizzesPanel/QuizComponent.vue'
import { type Quiz } from '@/Types/Quiz'
import {Request} from '@/scripts/request'
const props = defineProps<{ quizzes: Quiz[] }>()
const selectedQuiz = ref<number>()
const showLockedQuizzes = ref<boolean>(true)
const quizzesRef = ref<Quiz[]>(props.quizzes)
watch(
  () => props.quizzes,
  (updatedQuizzes : Quiz[]) => quizzesRef.value = updatedQuizzes,
)

provide('quizzes', quizzesRef)

const request = new Request()

function addQuiz() {
  request.sendRequest('/admin/quizzes','POST',{ name: 'Nowy test' })
}

function toggleQuizView(quiz: Quiz) {
  selectedQuiz.value = selectedQuiz.value === quiz.id ? undefined : quiz.id
}
</script>

<template>
  <div class="flex flex-col w-full pb-3">
    <div data-name="toolbar" class="flex gap-5 bg-white/70 px-4 py-2 backdrop-blur-md shadow">
      <button class="flex gap-2 hover:bg-primary/5 duration-200 p-2 rounded-lg"> <FilterIcon /> Filtruj </button>
      <button class="flex gap-2 hover:bg-primary/5 duration-200 p-2 rounded-lg"> <SortIcon /> Sortuj </button>
      <button class="flex gap-2 hover:bg-primary/5 duration-200 p-2 rounded-lg" @click="showLockedQuizzes=!showLockedQuizzes"> <EyeDynamicIcon :is-opened="showLockedQuizzes" /> {{ showLockedQuizzes ? 'Pokaż' : 'Schowaj' }} zablokowane </button>
      <div class="flex-1" />
      <button :disabled="request.isRequestOngoing.value" :class="{'opacity-70':request.isRequestOngoing.value}" class="font-bold" @click="addQuiz">+&nbsp;Dodaj&nbsp;test</button>
    </div>
    <div v-for="quiz of quizzesRef" :key="quiz.id" class="px-4">
      <QuizComponent
        :quiz="quiz"
        :is-selected="selectedQuiz===quiz.id"
        :show-locked-quizzes="showLockedQuizzes"
        @display-toggle="toggleQuizView"
      />
    </div>
  </div>
</template>
