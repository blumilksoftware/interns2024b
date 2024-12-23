<script setup lang="ts">
import { type Errors } from '@inertiajs/core'
import CustomDatepicker from '@/components/Common/CustomDatepicker.vue'
import { formatDate } from '@/Helpers/Format'
import CrudInput from '@/components/Crud/CrudInput.vue'
import { computed } from 'vue'
import vDynamicTextAreaHeight from '@/Helpers/vDynamicTextAreaHeight'

defineProps<{
  editing: boolean
  selected: boolean
  errors: Errors
}>()
const quiz = defineModel<Quiz>({ required: true })
const publicAvailabityString = computed(() => quiz.value.isPublic ? 'Dostępny dla wszystkich' : 'Dostępny tylko dla zaproszonych')
const quizModeString = computed(() => quiz.value.isLocal ? 'Offline' : 'Online')
</script>

<template>
  <div class="flex flex-col w-full px-2">
    <CrudInput
      v-model="quiz.title"
      name="title"
      :error="errors.title"
      :editing
      large
    />

    <CrudInput
      v-model="quiz.scheduledAt"
      name="scheduled"
      label="Rozpoczęcie testu:"
      :error="errors.scheduled_at"
      :editing
      :selected
      :format="formatDate"
    >
      <CustomDatepicker
        v-model="quiz.scheduledAt"
        :error="errors.scheduled_at"
        :format="formatDate"
      />
    </CrudInput>

    <CrudInput
      v-model="quiz.duration"
      name="duration"
      type="number"
      min="1"
      label="Czas trwania testu (min):"
      :error="errors.duration"
      :editing
      :selected
    />
    
    <CrudInput
      v-model="publicAvailabityString"
      label="Widoczność:"
      :error="errors.is_public"
      :editing
      :selected
    >
      <button
        class="font-bold text-primary border-b border-primary/30 hover:border-primary transition-colors"
        @click="quiz.isPublic = !quiz.isPublic"
      >
        {{ publicAvailabityString }}
      </button>
    </CrudInput>
    
    <CrudInput
      v-model="quizModeString"
      label="Tryb:"
      :error="errors.is_local"
      :editing
      :selected
    >
      <button
        class="font-bold text-primary border-b border-primary/30 hover:border-primary transition-colors"
        @click="quiz.isLocal = !quiz.isLocal"
      >
        {{ quizModeString }}
      </button>
    </CrudInput>

    <Transition>
      <CrudInput
        v-if="quiz.description || editing"
        v-model="quiz.description"
        label="Opis:"
        :column="selected"
        :error="errors.is_public"
        :editing
        :selected
      >
        <textarea
          v-model="quiz.description"
          v-dynamic-text-area-height
          name="name"
          autocomplete="off"
          class="size-full bg-transparent outline-none font-bold"
          :disabled="!editing"
          :class="{
            'border-b border-b-primary/30 duration-200 transition-colors hover:border-b-primary/60 text-primary' : editing,
            'border-b-red' : errors.name
          }"
        />
      </CrudInput>
    </Transition>
  </div>
</template>
