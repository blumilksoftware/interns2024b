<script setup lang="ts">
import { computed, ref } from 'vue'
import { CheckIcon, CloudArrowUpIcon, DocumentDuplicateIcon, PencilIcon, PlayIcon, TrashIcon, UserPlusIcon, UsersIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { CloudArrowDownIcon } from '@heroicons/vue/20/solid'
import RequestWrapper from '@/components/Common/RequestWrapper.vue'
import { formatDate } from '@/Helpers/Format'
import WarningMessageBox from '@/components/Common/WarningMessageBox.vue'

const props = defineProps<{
  archived:boolean
  unlocked:boolean
  locked:boolean
  editing:boolean
  startTimeNotReached:boolean
}>()
const quiz = defineModel<Quiz>({ required: true })
const emit = defineEmits<{ toggleEditing:[editing:boolean], cancelChanges:[] }>()
const showDeleteMessage = ref(false)

const questionsHaveOneCorrectAnswer = computed(
  ()=> quiz.value.questions.every(
    (question: Question) => {
      const correctIdx = question.answers.findIndex(answer => answer.correct)
      return correctIdx !== -1 && question.answers.slice(correctIdx+1).every(
        (answer:Answer) => !answer.correct,
      )
    },
  ),
)

const assertions = computed<Record<string, [boolean, string]>>(()=>({
  hasCorrectAnswers: [questionsHaveOneCorrectAnswer.value, 'Żadne pytanie nie zawiera zaznaczonej prawidłowej odpowiedzi.'],
  hasQuestions: [quiz.value.questions.length > 0, 'Test nie zawiera żadnego pytania.'],
  duration: [!!quiz.value.duration, 'Czas trwania testu nie jest ustawiony.'],
  startTimeNotReached: [props.startTimeNotReached, 'Czas rozpoczęcia testu upłynął.'],
}))

const publishValidation = computed(
  () => validation(
    assertions.value.hasCorrectAnswers,
    assertions.value.hasQuestions,
    assertions.value.duration,
    assertions.value.startTimeNotReached,
  ),
)

const quizDemoValidation = computed(
  () => validation(
    assertions.value.hasCorrectAnswers,
    assertions.value.hasQuestions,
    assertions.value.duration,
  ),
)

function validation(...assertionPairs: Array<[boolean, string]>): { validated: boolean, error: string } {
  const validationOutcome = { validated: true, error: '' }

  for (const [isValid, errorMsg] of assertionPairs) {
    validationOutcome.validated &&= isValid
    if (!isValid && !validationOutcome.error) {
      validationOutcome.error = errorMsg
    }
  }

  return validationOutcome
}

function sanitizeData() {
  for (const question of quiz.value.questions)
    question.answers = question.answers.filter(answer => answer.text && answer.text.trim() !== '')
}
</script>

<template>
  <WarningMessageBox :open="showDeleteMessage" @close="showDeleteMessage = false">
    <template #message>
      <b class="text-[1.1rem] text-gray-900">Usunąć test "{{ quiz.title }}"?</b>
      <p class="text-gray-500">Test zostanie usunięty bezpowrotnie.</p>
    </template>

    <template #buttons>
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
  </WarningMessageBox>

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
        :data="{ ...quiz, scheduledAt: formatDate(quiz.scheduledAt, false) }"
        @success="emit('toggleEditing', false); sanitizeData()"
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

    <button v-if="!archived && !locked" title="Usuń test" @click="showDeleteMessage = true">
      <TrashIcon class="icon slide-up-animation text-red hover:text-red-500" />
    </button>

    <RequestWrapper
      v-if="!editing"
      :href="`/admin/quizzes/${quiz.id}`"
      :title="!quizDemoValidation.validated ? `Nie można wyświetlić demonstracji testu. ${quizDemoValidation.error}` : 'Włącz demonstrację testu'"
      :disabled="!quizDemoValidation.validated"
    >
      <PlayIcon
        class="w-7.5 h-7.5 text-primary stroke-2"
        :class="{
          'opacity-50': !quizDemoValidation.validated,
          'hover:text-primary-800 slide-up-animation': quizDemoValidation.validated
        }"
      />
    </RequestWrapper>

    <RequestWrapper
      v-if="!editing && unlocked"
      method="post"
      :href="`/admin/quizzes/${quiz.id}/lock`"
      :title="!publishValidation.validated ? `Nie można opublikować testu. ${publishValidation.error}` : 'Udostępnij test publicznie'"
      :disabled="!publishValidation.validated"
      @click="quiz.state = 'locked'"
    >
      <CloudArrowUpIcon
        class="w-7.5 h-7.5 text-primary stroke-2"
        :class="{
          'opacity-50': !publishValidation.validated,
          'hover:text-primary-800 slide-up-animation': publishValidation.validated
        }"
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
