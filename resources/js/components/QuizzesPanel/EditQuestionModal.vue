<script setup lang="ts">
import { type Question } from '@/Types/Question'
import AnswerComponenent from '@/components/Common/AnswerComponenent.vue'
import { ref } from 'vue'

const props = defineProps<{ question:Question }>()
const isVisible = defineModel<boolean>('isVisible')

function setHidden() {
  isVisible.value = false
}

const correctAnswer = ref<number|undefined>(props.question.correct)

function markAnswerAsCorrect(answerId : number) {
  correctAnswer.value = answerId
  console.log(props.question.correct) 
}

</script>

<template>
  <Transition>
    <div
      v-show="isVisible"
      class="inset-0 h-screen w-full bg-white fixed z-20 p-5 flex flex-col gap-2"
    >
      <b class="text-[1.1rem]">Pytanie {{ question.id }}</b>
      <textarea class="h-auto ring ring-primary/30 p-2 rounded" rows="2" :value="question.text" />
      <b class="text-[1.1rem]">Odpowiedzi </b>
      <div v-for="answer of question.answers" :key="answer.id">
        <AnswerComponenent 
          :id="answer.id"
          v-model:text="answer.text"
          :is-correct="correctAnswer===answer.id" 
          @check-click="markAnswerAsCorrect(answer.id)"
        />
      </div>
      <footer class="fixed bottom-0 flex justify-end w-full p-2 pr-8">
        <button class="bg-primary text-white rounded-lg py-2 px-4 font-bold" @click="setHidden">Zachowaj</button>
      </footer>
    </div>
  </Transition>
</template>
