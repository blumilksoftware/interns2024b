<script setup lang="ts">
import { computed, ref } from 'vue'
import { CheckIcon, CloudArrowUpIcon, DocumentDuplicateIcon, ExclamationTriangleIcon, PencilIcon, TrashIcon, UserPlusIcon, UsersIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { CloudArrowDownIcon } from '@heroicons/vue/20/solid'
import RequestWrapper from '@/components/Common/RequestWrapper.vue'
import MessageBox from '@/components/Common/MessageBox.vue'
import type Quiz from '@/Types/Quiz'
import type Question from '@/Types/Question'
import type Answer from '@/Types/Answer'

const props = defineProps<{
  quiz:Quiz
  archived:boolean
  unlocked:boolean
  locked:boolean
  editing:boolean
  startTimeReached:boolean
}>()
const emit = defineEmits<{ toggleEditing:[editing:boolean], cancelChanges:[] }>()
const showDeleteMessage = ref(false)

function hasOneCorrectAnswer() {
  return props.quiz.questions.every(
    (question: Question) => {
      const correctIdx = question.answers.findIndex(answer => answer.correct)
      return correctIdx !== -1 && question.answers.slice(correctIdx+1).every(
        (answer:Answer) => !answer.correct,
      )
    },
  )
}

const isDraftValidated = computed(() =>
  props.quiz.duration &&
  props.startTimeReached &&
  props.quiz.questions.length > 0 &&
  hasOneCorrectAnswer(),
)
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
      >
        Usuń
      </RequestWrapper>
    </template>
  </MessageBox>
  
  <div class="flex gap-5 pl-5 h-fit">
    <a
      v-if="locked"
      title="Zaproś uczęstników"
      class="flex items-center"
      :href="`/admin/quizzes/${quiz.id}/invite`"
    >
      <UserPlusIcon class="icon" />
    </a>

    <button v-if="unlocked && !editing" title="Edytuj test" @click="emit('toggleEditing', true)">
      <PencilIcon class="icon" />
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
        preserve-scroll
        preserve-state
        method="patch"
        :href="`/admin/quizzes/${quiz.id}`"
        :data="quiz"
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
      <DocumentDuplicateIcon class="icon" />
    </RequestWrapper>

    <button v-if="!archived" title="Usuń test" @click="showDeleteMessage=true">
      <TrashIcon class="icon text-red hover:text-red-500" />
    </button>
    
    <RequestWrapper
      v-if="!editing && unlocked"
      class="rounded-xl"
      method="post"
      :href="`/admin/quizzes/${quiz.id}/lock`"
      :title="!isDraftValidated ? 'Nie można oddać testu. Uzupełnij brakujące dane' : 'Udostępnij test publicznie'"
      :disabled="!isDraftValidated"
    >
      <CloudArrowUpIcon
        class="h-7.5 text-primary stroke-2"
        :class="{ 'opacity-50' : !isDraftValidated, 'hover:text-primary-800' : isDraftValidated}"
      />
    </RequestWrapper>

    <RequestWrapper
      v-if="locked"
      method="post"
      title="Cofnij publikację"
      :href="`/admin/quizzes/${quiz.id}/unlock`"
    >
      <CloudArrowDownIcon class="icon w-7.5 h-7.5" />
    </RequestWrapper>

    <a
      v-if="archived"
      title="Przejdź do rankingu"
      :href="`/admin/quizzes/${quiz.id}/ranking`"
    >
      <UsersIcon class="icon" />
    </a>
  </div>
</template>
