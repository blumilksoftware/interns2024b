<script setup lang="ts">
import { computed, inject, ref, watch, type Ref } from 'vue'
import { vAutoAnimate } from '@formkit/auto-animate'
import { PlusCircleIcon } from '@heroicons/vue/24/outline'
import QuestionComponent from '@/components/QuizzesPanel/QuestionComponent.vue'
import QuizHeader from '@/components/QuizzesPanel/QuizHeader.vue'
import QuizNavbar from '@/components/QuizzesPanel/QuizNavbar.vue'
import ExpansionToggleDynamicIcon from '@/components/Icons/ExpansionToggleDynamicIcon.vue'
import getKey from '@/Helpers/KeysManager'
import useRequestResolution from '@/Helpers/RequestResolution'
import { formatDate } from '@/Helpers/Format'
import InputWrapper from '@/components/QuizzesPanel/InputWrapper.vue'

const currentTime = inject<Ref<number>>('currentTime')
const props = defineProps<{ quiz: Quiz }>()
const quiz = ref<Quiz>(JSON.parse(JSON.stringify(props.quiz)))
const selected = ref(false)
const editing = ref(false)
const archived = computed(() => quiz.value.state === 'published')
const { processing, errors } = useRequestResolution()
const startTimeReached = computed<boolean>(() => !!quiz.value.scheduledAt &&
  !!currentTime?.value &&
  Date.parse(quiz.value.scheduledAt) < currentTime.value)

watch(startTimeReached, quizHasStarted => {
  if (quizHasStarted && quiz.value.state === 'locked')
    quiz.value.state = 'published'
})

function addQuestion() {
  quiz.value.questions.push({
    text: '',
    answers: [],
  })
}

function copyQuestion(question: Question) {
  quiz.value.questions.push(JSON.parse(JSON.stringify({ ...question, id: undefined })))
}

function deleteQuestion(idx: number, question: Question) {
  quiz.value.questions = quiz.value.questions.filter((q: Question) => q !== question)
  if (errors.value[`questions.${idx}.text`]) {
    errors.value[`questions.${idx}.text`] = ''
  }
}

function toggleSelection(isSelected: boolean) {
  selected.value = editing.value || isSelected
}

function toggleEditing(isEditing: boolean){
  editing.value = isEditing
  toggleSelection(isEditing)
}
</script>

<template>
  <div
    v-auto-animate
    class="flex flex-col gap-5 p-5 bg-white/70 border-2 rounded-xl shadow-sm"
  >
    <Transition>
      <div
        v-show="processing"
        class="absolute bg-white/50 backdrop-blur-md z-10 size-full left-0 flex items-center justify-center -mt-4 rounded-xl"
      >
        <div
          class="inline-block size-8 animate-spin rounded-full border-4 border-solid border-current border-e-transparent align-[-0.125em] text-primary motion-reduce:animate-[spin_1.5s_linear_infinite]"
          role="status"
        />
      </div>
    </Transition>

    <div class="flex justify-between">
      <button
        :disabled="editing"
        class="h-7 disabled:opacity-50"
        @click="toggleSelection(!selected)"
      >
        <ExpansionToggleDynamicIcon
          class="text-primary stroke-4 w-4"
          :expanded="selected"
        />
      </button>

      <QuizHeader
        v-model="quiz"
        class="hidden sm:flex"
        :editing="editing"
        :selected="selected"
        :errors="errors"
      />

      <QuizNavbar
        v-model="quiz"
        :unlocked="quiz.state === 'unlocked'"
        :locked="quiz.state === 'locked'"
        :archived="archived"
        :editing="editing"
        :start-time-not-reached="!startTimeReached"
        @toggle-editing="toggleEditing"
        @cancel-changes="quiz = JSON.parse(JSON.stringify(props.quiz)); errors={}"
      />
    </div>

    <QuizHeader
      v-model="quiz"
      class="sm:hidden -mt-3"
      :editing="editing"
      :selected="selected"
      :errors="errors"
    />

    <template v-if="selected">
      <InputWrapper
        v-for="(question, idx) of quiz.questions"
        :key="getKey(question)"
        :error="errors[`questions.${idx}.text`] ?? ''"
      >
        <QuestionComponent
          v-model="quiz.questions[idx]"
          :error="errors[`questions.${idx}.text`] ?? ''"
          :editing="editing"
          :index="idx"
          :questions-total="quiz.questions.length"
          @copy="copyQuestion"
          @delete="deleteQuestion"
        />
      </InputWrapper>
    </template>

    <button
      v-if="editing"
      class="icon-button px-2"
      @click="addQuestion"
    >
      <PlusCircleIcon class="icon" /> Dodaj pytanie
    </button>

    <footer
      v-if="selected"
      class="flex flex-col justify-end text-right sm:flex-row gap-x-4"
    >
      <span class="text-gray-400 text-sm">
        Utworzony: {{ formatDate(quiz.createdAt) }}
      </span>

      <span class="text-gray-400 text-sm">
        Ostatnio edytowany: {{ formatDate(quiz.updatedAt) }}
      </span>
    </footer>
  </div>
</template>
