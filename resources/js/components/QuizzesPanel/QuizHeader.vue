<script setup lang="ts">
import { type Errors } from '@inertiajs/core'
import CustomDatepicker from '@/components/Common/CustomDatepicker.vue'
import { formatDate } from '@/Helpers/Format'
import CrudInput from '@/components/Crud/CrudInput.vue'

defineProps<{
  editing: boolean
  selected: boolean
  errors: Errors
}>()
const quiz = defineModel<Quiz>({ required: true })
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

    <div
      class="flex items-center duration-200 min-h-6.5"
      :class="{ 'text-sm text-gray-600' : !selected }"
    >
      <p>Widoczność: </p>

      <label
        class="inline-flex items-center cursor-pointer border-b border-transparent bg-transparent focus:border-b-primary"
        :class="{'pl-2 border-b-primary/30 hover:border-b-primary/60 text-primary text-center duration-200 transition-colors': editing}"
      >
        <input
          v-if="editing"
          type="checkbox"
          class="sr-only peer"
          :checked="quiz.isPublic"
          @click="(event: any) => quiz.isPublic = event.currentTarget.checked"
        >

        <div
          v-if="editing"
          class="
          relative
          w-7
          h-4
          bg-primary
          peer-focus:outline-none
          peer-focus:ring-2
          rounded-full

          after:bg-white
          after:content-['']
          after:start-0.5
          after:absolute after:top-0.5
          after:border
          after:rounded-full
          after:size-3
          peer-checked:after:translate-x-full
          rtl:peer-checked:after:-translate-x-full
          after:transition-all
          "
        />

        <b
          class="select-none"
          :class="{'pl-2 text-primary': editing, 'pl-1': !editing}"
        >
          {{ quiz.isPublic ? 'Dostępny dla wszystkich' : 'Dostępny tylko dla zaproszonych' }}
        </b>
      </label>
    </div>
  </div>
</template>
