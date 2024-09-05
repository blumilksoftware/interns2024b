<script setup lang="ts">
import { ref, watch } from 'vue'
import {Request} from '@/scripts/request'
import SortIcon from '@/components/Icons/SortIcon.vue'
import EyeDynamicIcon from '@/components/Icons/EyeDynamicIcon.vue'
import FilterIcon from '@/components/Icons/FilterIcon.vue'
import QuizComponent from '@/components/QuizzesPanel/QuizComponent.vue'
import { type Quiz } from '@/Types/Quiz'
import { type Question } from '@/Types/Question'
import { type Answer } from '@/Types/Answer'
const props = defineProps<{ quizzes: Quiz[] }>()
const selectedQuiz = ref<number>()
const showLockedQuizzes = ref<boolean>(true)
const quizzes = ref<Quiz[]>(addKeys(props.quizzes))
watch(
  () => props.quizzes,
  (updatedQuizzes : Quiz[]) => quizzes.value = addKeys(updatedQuizzes),
)

function addKeys(quizzes:Quiz[]) {
  return quizzes.map(
    (quiz: Quiz)=>{
      return {
        ...quiz, key: quiz.key ?? quiz.id, questions: quiz.questions.map(
          (q:Question)=>{
            return {
              ...q, key: q.key ?? q.id, answers: q.answers.map(
                (ans:Answer)=>{
                  return {...ans, key: quiz.key ?? ans.id}
                },
              ),
            }
          },
        ),
      }
    },
  )
}

const request = new Request()

function addQuiz() {
  request.sendRequest('/admin/quizzes',{ method: 'post', data:{name: 'Nowy test'} })
}

function toggleQuizView(quiz: Quiz) {
  selectedQuiz.value = selectedQuiz.value === quiz.id ? undefined : quiz.id
}
</script>

<template>
  <div class="flex flex-col w-full pb-3">
    <div data-name="toolbar" class="flex gap-5 px-6 backdrop-blur-md">
      <button class="flex gap-2 hover:bg-primary/5 duration-200 p-2 rounded-lg"> <FilterIcon /> Filtruj </button>
      <button class="flex gap-2 hover:bg-primary/5 duration-200 p-2 rounded-lg"> <SortIcon /> Sortuj </button>
      <button class="flex gap-2 hover:bg-primary/5 duration-200 p-2 rounded-lg" @click="showLockedQuizzes=!showLockedQuizzes"> <EyeDynamicIcon :is-opened="showLockedQuizzes" /> {{ showLockedQuizzes ? 'Pokaż' : 'Schowaj' }} zablokowane </button>
      <div class="flex-1" />
      <button :disabled="request.isRequestOngoing.value" :class="{'opacity-70':request.isRequestOngoing.value}" class="font-bold" @click="addQuiz">+&nbsp;Dodaj&nbsp;test</button>
    </div>
    <div v-for="(quiz, idx) of quizzes" :key="quiz.id" class="px-4">
      <QuizComponent
        v-model="quizzes[idx]"
        :is-selected="selectedQuiz===quiz.id"
        :show-locked-quizzes="showLockedQuizzes"
        @display-toggle="toggleQuizView"
      />
    </div>
  </div>
</template>
