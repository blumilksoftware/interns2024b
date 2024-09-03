<!-- eslint-disable @typescript-eslint/no-unused-vars -->
<script setup lang="ts">
import ExapnsionToggleDynamicIcon from '../Icons/ExapnsionToggleDynamicIcon.vue'
import TrashIcon from '@/components/Icons/TrashIcon.vue'
import CopyIcon from '@/components/Icons/CopyIcon.vue'
import CheckDynamicIcon from '@/components/Icons/CheckDynamicIcon.vue'

import { computed, inject, ref, type Ref } from 'vue'
import { type Question } from '@/Types/Question'
import { type Option } from '@/Types/Option'
import { type CleanAnswer } from '@/Types/CleanAnswer'
import { type CleanQuestion } from '@/Types/CleanQuestion'
import { type Quiz } from '@/Types/Quiz'
import Dropdown from '../Common/Dropdown.vue'

defineProps<{ quizId: number }>()
const emit = defineEmits<{ delete: [question: CleanQuestion] }>()
const question = defineModel<Question>({ required: true })
const isAnswerExpanded = ref<boolean>(false)
const hasAnswers = computed(() => question.value.answers.length > 0)
const quizzesRef = inject<Ref<Quiz[]>>('quizzes', ref<any[]>([]))

const copyingOptions = computed(
  ():Option[] => quizzesRef.value.map(quiz => ({ id: quiz.id, text: quiz.name }),
  ),
)

function deleteQuestion() {
  emit('delete', question.value)
}

function copyQuestion(quizId:number) {
  for (const quiz of quizzesRef.value) {
    if (quiz.id === quizId) {
      const newQuestion:Question = JSON.parse(JSON.stringify(question.value))
      newQuestion.id++
      quiz.questions.push(newQuestion) 
      return
    }
  }
}

function addAnswer() {
  const answer: CleanAnswer = {
    text: 'Nowa odpowiedź',
    correct: false,
  }
  question.value.answers.push(answer)
  isAnswerExpanded.value = true
}
function deleteAnswer(answer: CleanAnswer) {
  question.value.answers = question.value.answers.filter(ans => ans !== answer)
}
function setCorrectAnswer(answer: CleanAnswer) {
  for (const ans of question.value.answers)
    ans.correct = false
  answer.correct = true
}
</script>

<template>
  <div class="rounded-lg border border-primary/30 flex justify-between">
    <div class="flex flex-col gap-4 p-4 w-full">
      <div class="flex flex-col gap-1">
        <b class="text-[1.1rem]">Pytanie {{ question.id }}</b>
        <p>{{ question.text }}</p>
      </div>
      <div class="flex gap-4 ">
        <button class="w-fit border border-primary/30 rounded-lg py-2 px-3 gap-2 flex bg-white/50" @click="addAnswer">
          <b>+ Dodaj odpowiedź</b>
        </button>
        <button v-if="hasAnswers" class="w-fit border border-primary/30 rounded-lg py-2 px-3 gap-2 flex bg-white/50"
                @click="isAnswerExpanded = !isAnswerExpanded"
        >
          <b>Odpowiedzi</b>
          <ExapnsionToggleDynamicIcon :is-expanded="isAnswerExpanded" />
        </button>
      </div>
      <ol v-if="isAnswerExpanded && hasAnswers" class="flex flex-col gap-4 w-full">
        <li v-for="answer of question.answers" :key="answer.id"
            class="w-full flex border border-primary/30 rounded-lg bg-white/50"
        >
          <button class="p-4 flex items-center gap-4 w-full" @click="setCorrectAnswer(answer)">
            <CheckDynamicIcon class="size-6" :is-correct="answer.correct" />
            {{ answer.text }}
          </button>
          <div class="px-3 border-l border-primary/30 flex flex-col justify-evenly">
            <button data-name="delete" @click="deleteAnswer(answer)">
              <TrashIcon />
            </button>
          </div>
        </li>
      </ol>
    </div>
    <div class="px-3 border-l border-primary/30 flex flex-col justify-evenly">
      <Dropdown :options="copyingOptions" @option-click="copyQuestion">
        <CopyIcon />
      </Dropdown>
      <button @click="deleteQuestion"><TrashIcon /></button>
    </div>
  </div>
</template>

<style scoped>
ol>li::marker {
  font-weight: bold;
}
</style>
