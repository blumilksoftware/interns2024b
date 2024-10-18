<script setup lang="ts">
import { computed, ref } from 'vue'
import { DocumentDuplicateIcon, PlusCircleIcon, TrashIcon } from '@heroicons/vue/24/outline'
import { vAutoAnimate } from '@formkit/auto-animate'
import ExapnsionToggleDynamicIcon from '@/components/Icons/ExapnsionToggleDynamicIcon.vue'
import AnswerComponent from '@/components/QuizzesPanel/AnswerComponent.vue'
import getKey from '@/Helpers/KeysManager'
import type Answer from '@/Types/Answer'
import type Question from '@/Types/Question'

defineProps<{ editing:boolean, index:number, questionsTotal:number }>()
const emit = defineEmits<{ copy: [question:Question], delete: [question:Question] }>()
const question = defineModel<Question>({ required: true })
const answersPaneExpanded = ref<boolean>(false)
const hasAnswers = computed(() => question.value.answers.length > 0)

function addAnswer() {
  const answer: Answer = {
    text: 'Nowa odpowiedź',
    correct: false,
  }
  question.value.answers.push(answer)
  answersPaneExpanded.value = true
}

function deleteAnswer(currentAnswer: Answer) {
  question.value.answers = question.value.answers.filter((answer:Answer) => answer !== currentAnswer)
}

function setCorrectAnswer(currentAnswer: Answer) {
  for (const answer of question.value.answers)
    answer.correct = false
  currentAnswer.correct = true
}
</script>

<template>
  <div v-auto-animate class="flex flex-col gap-5 p-5 w-full rounded-lg border border-primary/30">
    <div class="flex flex-col gap-1.5">
      <div class="flex justify-between">
        <b class="text-lg">Pytanie {{ `${index+1}/${questionsTotal}` }}</b>
        <div v-if="editing" class="flex gap-5">
          <button title="Skopiuj pytanie" @click="emit('copy', question)">
            <DocumentDuplicateIcon class="icon" />
          </button>
          <button title="Usuń pytanie" @click="emit('delete', question)">
            <TrashIcon class="icon text-red hover:text-red-500" />
          </button>
        </div>
      </div>
      
      <span v-if="!editing">{{ question.text }}</span>
      <textarea v-else v-model="question.text" class="h-12 w-full bg-transparent outline-none border-b border-primary/30 focus:border-primary/60" />
    </div>
      
    <button
      v-if="hasAnswers"
      class="flex gap-1.5 font-bold text-primary hover:text-primary-800 items-center text-percentage-105"
      @click="answersPaneExpanded = !answersPaneExpanded"
    >
      Odpowiedzi
      <ExapnsionToggleDynamicIcon class="size-3.5 stroke-[4]" :expanded="answersPaneExpanded" />
    </button>

    <template v-if="answersPaneExpanded && hasAnswers">
      <AnswerComponent
        v-for="(answer, idx) of question.answers"
        :key="getKey(answer)"
        v-model="question.answers[idx]"
        class="-mt-3"
        :editing="editing"
        @delete="deleteAnswer"
        @set-correct="setCorrectAnswer"
      />
    </template>

    <button v-if="editing" class="icon-button" @click="addAnswer">
      <PlusCircleIcon class="icon" /> Dodaj odpowiedź
    </button>
  </div>
</template>
