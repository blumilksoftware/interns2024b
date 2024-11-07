<script setup lang="ts">
import { computed, ref } from 'vue'
import { CheckIcon, CloudArrowUpIcon, DocumentDuplicateIcon, ExclamationTriangleIcon, PencilIcon, TrashIcon, UserPlusIcon, UsersIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { CloudArrowDownIcon } from '@heroicons/vue/20/solid'
import {type Errors} from '@inertiajs/core'
import RequestWrapper from '@/components/Common/RequestWrapper.vue'
import MessageBox from '@/components/Common/MessageBox.vue'
import { formatDate } from '@/Helpers/Format'

const props = defineProps<{
  archived:boolean
  unlocked:boolean
  locked:boolean
  editing:boolean
  startTimeReached:boolean
}>()
const quiz = defineModel<Quiz>({ required: true })
const emit = defineEmits<{ toggleEditing:[editing:boolean], cancelChanges:[] }>()
const showDeleteMessage = ref(false)
const publishValidation = computed<{ validated: boolean, error: string }>(
  () => {
    const validations: Record<string, boolean> = {
      hasCorrectAnswers: questionsHaveOneCorrectAnswer(),
      hasQuestions: quiz.value.questions.length > 0,
      duration: !!quiz.value.duration,
      startTimeReached: props.startTimeReached,
    }
    const errors: Errors = {
      hasCorrectAnswers: 'Pytanie nie zawiera zaznaczonej prawidłowej odpowiedzi.',
      hasQuestions: 'Test nie zawiera żadnego pytania.',
      duration: 'Czas trwania testu nie jest ustawiony.',
      startTimeReached: 'Czas rozpoczęcia testu upłynął.',
    }
    const validationOutcome = { validated: true,  error: ''}
    for (const key in validations) {
      validationOutcome.validated &&= validations[key]
      if (!validations[key])
        validationOutcome.error = errors[key]
    }
    return validationOutcome
  },
)

function questionsHaveOneCorrectAnswer() {
  return quiz.value.questions.every(
    (question: Question) => {
      const correctIdx = question.answers.findIndex(answer => answer.correct)
      return correctIdx !== -1 && question.answers.slice(correctIdx+1).every(
        (answer:Answer) => !answer.correct,
      )
    },
  )
}
</script>

<template>
  <MessageBox :open="showDeleteMessage" @close="showDeleteMessage = false">
    <template #message>
      <div class="flex gap-5">
        <div class="bg-red/10 p-5 rounded-full h-fit">
          <ExclamationTriangleIcon class="size-6 text-red" />
        </div>
        <div>
          <b class="text-[1.1rem] text-gray-900">Usunąć test "{{ quiz.title }}"?</b>
          <p class="text-gray-500">Test zostanie usunięty bezpowrotnie.</p>
        </div>
      </div>
    </template>

    <template #buttons>
      <button class="px-2 font-bold" @click="showDeleteMessage = false">
        Anuluj
      </button>

      <RequestWrapper
        class="bg-red font-bold text-white rounded-lg px-4 py-2"
        title="Usuń test"
        method="delete"
        :href="`/admin/quizzes/${quiz.id}`"
        @click="showDeleteMessage = false"
      >
        Usuń
      </RequestWrapper>
    </template>
  </MessageBox>
  
  <div class="flex gap-5 pl-5 h-fit">
    <a
      v-if="locked"
      title="Zaproś uczestników"
      class="flex items-center"
    >
      <UserPlusIcon class="icon slide-up-animation" />
    </a>

    <button v-if="unlocked && !editing" title="Edytuj test" @click="emit('toggleEditing', true)">
      <PencilIcon class="icon slide-up-animation" />
    </button>

    <template v-if="editing">
      <button
        title="Anuluj edytowanie testu"
        @click="() => { emit('cancelChanges'); emit('toggleEditing', false) }"
      >
        <XMarkIcon class="icon" />
      </button>

      <RequestWrapper
        title="Zapisz zmiany"
        method="patch"
        :href="`/admin/quizzes/${quiz.id}`"
        :data="{ ...quiz, scheduledAt: formatDate(quiz.scheduledAt) }"
        @success="emit('toggleEditing', false)"
      >
        <CheckIcon class="icon" title="Zapisz edytowany test" />
      </RequestWrapper>
    </template>

    <RequestWrapper
      v-if="!editing" 
      title="Skopiuj test"
      method="post"
      :href="`/admin/quizzes/${quiz.id}/clone`"
    >
      <DocumentDuplicateIcon class="icon slide-up-animation" />
    </RequestWrapper>

    <button v-if="!archived" title="Usuń test" @click="showDeleteMessage = true">
      <TrashIcon class="icon slide-up-animation text-red hover:text-red-500" />
    </button>
    
    <RequestWrapper
      v-if="!editing && unlocked"
      class="rounded-xl"
      method="post"
      :href="`/admin/quizzes/${quiz.id}/lock`"
      :title="!publishValidation.validated ? `Nie można opublikować testu. ${publishValidation.error}` : 'Udostępnij test publicznie'"
      :disabled="!publishValidation.validated"
      @click="quiz.state = 'locked'"
    >
      <CloudArrowUpIcon
        class="w-7.5 h-7.5 text-primary stroke-2"
        :class="{ 'opacity-50': !publishValidation.validated, 'hover:text-primary-800': publishValidation.validated }"
      />
    </RequestWrapper>

    <RequestWrapper
      v-if="locked"
      method="post"
      title="Cofnij publikację"
      :href="`/admin/quizzes/${quiz.id}/unlock`"
      @click="quiz.state = 'unlocked'"
    >
      <CloudArrowDownIcon class="icon slide-up-animation w-7.5 h-7.5" />
    </RequestWrapper>

    <a
      v-if="archived"
      title="Przejdź do rankingu"
      :href="`/admin/quizzes/${quiz.id}/ranking`"
    >
      <UsersIcon class="icon slide-up-animation" />
    </a>
  </div>
</template>
