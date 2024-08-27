<script setup lang="ts">
import { ref } from 'vue'
import SortIcon from '@/components/Icons/SortIcon.vue'
import EyeDynamicIcon from '@/components/Icons/EyeDynamicIcon.vue'
import FilterIcon from '@/components/Icons/FilterIcon.vue'
import QuizComponent from '@/components/QuizzesPanel/QuizComponent.vue'
import { type Quiz } from '@/Types/Quiz'
import { router } from '@inertiajs/vue3'
const props = defineProps<{ quizzes: Quiz[] }>()
const selectedQuiz = ref<number>()
const showLockedQuizzes = ref<boolean>(true)

function addQuiz() {
  router.post('/admin/quizzes', {
    name: 'Nowy test',
  })
}

function toggleQuizView(quiz: Quiz) {
  selectedQuiz.value = selectedQuiz.value === quiz.id ? undefined : quiz.id
}
</script>

<template>
  <div class="flex flex-col w-full">
    <div data-name="toolbar" class="flex gap-5 bg-white/70 px-4 py-2 backdrop-blur-md">
      <button class="flex gap-2 hover:bg-primary/5 duration-200 p-2 rounded-lg">
        <FilterIcon />
        Filtruj
      </button>
      <button class="flex gap-2 hover:bg-primary/5 duration-200 p-2 rounded-lg">
        <SortIcon />
        Sortuj
      </button>
      <button class="flex gap-2 hover:bg-primary/5 duration-200 p-2 rounded-lg" @click="showLockedQuizzes=!showLockedQuizzes">
        <EyeDynamicIcon :is-opened="showLockedQuizzes" />
        {{ showLockedQuizzes ? 'Pokaż' : 'Schowaj' }} zablokowane
      </button>
      <div class="flex-1" />
      <button class="font-bold" @click="addQuiz">+&nbsp;Dodaj&nbsp;test</button>
    </div>
    <div v-for="quiz in props.quizzes" :key="quiz.id" class="px-4">
      <QuizComponent
        :quiz="quiz"
        :is-selected="selectedQuiz===quiz.id"
        :show-locked-quizzes="showLockedQuizzes"
        @display-toggle="toggleQuizView"
      />
    </div>
  </div>
</template>
