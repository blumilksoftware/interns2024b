<script setup lang="ts">
import { ref, watch } from 'vue'
import {Request} from '@/scripts/request'
import SortIcon from '@/components/Icons/SortIcon.vue'
import EyeDynamicIcon from '@/components/Icons/EyeDynamicIcon.vue'
import QuizComponent from '@/components/QuizzesPanel/QuizComponent.vue'
import { type Quiz } from '@/Types/Quiz'
import Dropdown from '@/components/Common/Dropdown.vue'
import { Head } from '@inertiajs/vue3'
const props = defineProps<{ quizzes: Quiz[] }>()
const selectedQuiz = ref<number>()
const showLockedQuizzes = ref<boolean>(true)
const sorter = ref(sortByNameAscending)
const quizzes = ref<Quiz[]>(sorter.value(mapKeys(props.quizzes)))
watch(
  [props.quizzes, sorter],
  ([updatedQuizzes, sorted]) => quizzes.value = sorted(mapKeys(updatedQuizzes)),
)

function mapKeys<T extends {id: number, key?: number | string}>(array:T[]){
  for (const record of array){
    record.key ??= record.id
    for (const key in record)
      if (Array.isArray(record[key]))
        mapKeys(record[key])
  }
  return array
}


const request = new Request()

function addQuiz() {
  request.sendRequest('/admin/quizzes',{ method: 'post', data:{title: 'Nowy test'} })
}

function toggleQuizView(quiz: Quiz) {
  selectedQuiz.value = selectedQuiz.value === quiz.id ? undefined : quiz.id
}

function sortByNameAscending(arr : any[]){
  return arr.sort((a:Quiz, b:Quiz) => a.title.localeCompare(b.title))
}

function sortByNameDescending(arr : any[]){
  return arr.sort((a:Quiz, b:Quiz) => b.title.localeCompare(a.title))
}
</script>

<template>
  <Head>
    <title>Testy - Panel Adminu</title>
  </Head>
  <div class="flex flex-col w-full pb-3">
    <div data-name="toolbar" class="flex gap-5 px-6 backdrop-blur-md z-50">
      <Dropdown :options="[
        {key: 0, text: 'Sortuj według nazwy (A–Z)', action:()=>sorter=sortByNameAscending},
        {key: 1, text: 'Sortuj według nazwy (Z–A)', action:()=>sorter=sortByNameDescending},
      ]"
      > 
        <button class="flex gap-2 hover:bg-primary/5 duration-200 p-2 rounded-lg"> <SortIcon /> Sortuj </button>
      </Dropdown>
      <button class="flex gap-2 hover:bg-primary/5 duration-200 p-2 rounded-lg" @click="showLockedQuizzes=!showLockedQuizzes"> <EyeDynamicIcon :is-opened="showLockedQuizzes" /> {{ showLockedQuizzes ? 'Pokaż' : 'Schowaj' }} zablokowane </button>
      <div class="flex-1" />
      <button :disabled="request.isRequestOngoing.value" :class="{'opacity-70':request.isRequestOngoing.value}" class="bg-primary font-bold rounded-lg text-white px-4" @click="addQuiz">+ Dodaj test</button>
    </div>
    <div v-for="(quiz, idx) of quizzes" :key="quiz.id" class="px-4">
      <QuizComponent
        :quiz="quizzes[idx]"
        :is-selected="selectedQuiz===quiz.id"
        :show-locked-quizzes="showLockedQuizzes"
        @display-toggle="toggleQuizView"
      />
    </div>
  </div>
</template>

<style>
.datepicker {
  @apply bg-white/50 rounded-md ring-1 ring-primary/30 outline-none border-none font-bold
}
.datepicker-menu{
  @apply rounded-lg border border-primary/30
}
.dp__action_cancel{
  @apply text-base py-4 px-4 rounded-md
}
.dp__action_cancel:hover{
  @apply border-primary/30
}
.dp__action_select{
  @apply !bg-primary text-base font-bold py-4 px-4 rounded-md
}
.dp__action_select:hover{
  @apply bg-primary-950
}
.dp__active_date{
  @apply bg-primary font-bold
}
.dp__today{
  @apply border border-primary/30
}
.dp__btn {
  @apply fill-primary text-primary stroke-primary
}
.dp__btn:hover {
  @apply fill-primary-950 text-primary-950 stroke-primary-950
}
.dp__overlay {
  @apply rounded-lg
}
</style>
