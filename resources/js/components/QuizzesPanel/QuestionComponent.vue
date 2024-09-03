<!-- eslint-disable @typescript-eslint/no-unused-vars -->
<script setup lang="ts">
import ExapnsionToggleDynamicIcon from '../Icons/ExapnsionToggleDynamicIcon.vue'
import TrashIcon from '@/components/Icons/TrashIcon.vue'
import CopyIcon from '@/components/Icons/CopyIcon.vue'
import CheckDynamicIcon from '@/components/Icons/CheckDynamicIcon.vue'

import { computed, inject, ref, type Ref } from 'vue'
import { type Option } from '@/Types/Option'
import { type CleanAnswer } from '@/Types/CleanAnswer'
import { type CleanQuestion } from '@/Types/CleanQuestion'
import { type CleanQuiz } from '@/Types/CleanQuiz'
import Dropdown from '../Common/Dropdown.vue'
import EditableInput from '../Common/EditableInput.vue'

defineProps<{ quizId: number, isEditing: boolean, index:number, questionsLength:number}>()
const emit = defineEmits<{ delete: [question: CleanQuestion] }>()
const question = defineModel<CleanQuestion>({ required: true })
const isAnswerExpanded = ref<boolean>(false)
const hasAnswers = computed(() => question.value.answers.length > 0)
const quizzesRef = inject<Ref<CleanQuiz[]>>('quizzes', ref<any[]>([]))

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
      const newQuestion:CleanQuestion = JSON.parse(JSON.stringify(question.value))
      newQuestion.id++
      quiz.questions.push(newQuestion) 
      return
    }
  }
}

function addAnswer() {
  let newId = 0 
  for (const ans of question.value.answers) {
    if (newId < ans.id)
      newId = ans.id
  }
  const answer: CleanAnswer = {
    id: newId+1,
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
      <div class="flex flex-col gap-1.5">
        <b class="text-[1.1rem]">Pytanie {{ `${index+1}/${questionsLength}` }}</b>
        <span v-if="!isEditing">{{ question.text }}</span>
        <textarea v-else v-model="question.text" class="w-full p-2 border border-primary/30 rounded-md bg-white/50" />
      </div>
      <div v-if="isEditing || hasAnswers" class="flex gap-4 ">
        <button v-if="isEditing" class="w-fit border border-primary/30 rounded-lg py-2 px-3 gap-2 flex bg-white/50" @click="addAnswer">
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
            class="w-full flex gap-4 p-4 border border-primary/30 rounded-lg bg-white/50"
        >
          <button :disabled="!isEditing" @click="setCorrectAnswer(answer)">
            <CheckDynamicIcon class="size-6" :is-correct="answer.correct" />
          </button>
          <div class="flex items-center w-full">
            <!-- <EditableInput v-model="answer.text" :is-editing="isEditing" type="text" :bold="false" /> -->
            <span v-if="!isEditing">{{ answer.text }}</span>
            <textarea v-else v-model="answer.text" class="w-full p-2 bg-transparent border border-primary/30 rounded-md" />
          </div>
          <div class="flex flex-col justify-evenly">
            <button data-name="delete" @click="deleteAnswer(answer)">
              <TrashIcon />
            </button>
          </div>
        </li>
      </ol>
    </div>
    <div class="px-3 border-l border-primary/30 flex flex-col justify-evenly">
      <!-- copying is temporarily disabled -->
       
      <!-- <Dropdown :options="copyingOptions" @option-click="copyQuestion">
        <CopyIcon />
      </Dropdown> -->
      <button @click="deleteQuestion"><TrashIcon /></button>
    </div>
  </div>
</template>

<style scoped>
ol>li::marker {
  font-weight: bold;
}
</style>
