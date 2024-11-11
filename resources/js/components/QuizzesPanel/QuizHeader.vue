<script setup lang="ts">
import { type Errors } from '@inertiajs/core'
import InputWrapper from '@/components/QuizzesPanel/InputWrapper.vue'
import CustomDatepicker from '@/components/Common/CustomDatepicker.vue'
import vDynamicInputWidth from '@/Helpers/vDynamicInputWidth'
import { formatDate } from '@/Helpers/Format'

defineProps<{
  editing:boolean
  selected:boolean
  errors:Errors
}>()
const quiz = defineModel<Quiz>({ required: true })
</script>

<template>
  <div class="flex flex-col gap-1 w-full px-2">
    <InputWrapper
      :error="errors.title"
      :hide-error="!editing"
      :hide-content="!quiz.title && !editing"
    >
      <input
        v-model="quiz.title"
        v-dynamic-input-width
        type="text"
        name="title"
        autocomplete="off"
        class="w-full outline-none font-bold border-b border-transparent duration-200 transition-colors text-lg bg-transparent focus:border-b-primary"
        :class="{
          'border-b-primary/30 duration-200 transition-colors hover:border-b-primary/60 text-primary' : editing,
          'border-b-red' : errors.title && editing
        }"
        :disabled="!editing"
      >
    </InputWrapper>

    <div
      class="flex gap-1 duration-200 min-h-6.5"
      :class="{ 'text-sm text-gray-600' : !selected }"
    >
      <InputWrapper
        label="Rozpoczęcie testu:"
        :error="errors.scheduled_at"
        :hide-error="!editing"
        :hide-content="!quiz.scheduledAt && !editing"
      >
        <b v-if="!editing" class="whitespace-nowrap">{{ formatDate(quiz.scheduledAt) }}</b>
        <CustomDatepicker
          v-else
          v-model="quiz.scheduledAt"
          :error="errors.scheduled_at"
          :format="formatDate"
        />
      </InputWrapper>
    </div>

    <div
      class="flex gap-1 duration-200 min-h-6.5"
      :class="{ 'text-sm text-gray-600' : !selected }"
    >
      <InputWrapper
        label="Czas trwania testu (min):"
        :error="errors.duration"
        :hide-error="!editing"
        :hide-content="!quiz.duration && !editing"
      >
        <b v-if="!editing">{{ quiz.duration }}</b>
        <input
          v-else
          v-model="quiz.duration"
          v-dynamic-input-width
          type="number"
          name="title"
          min="1"
          autocomplete="off"
          class="text-md transition-none h-fit w-full outline-none font-bold border-b border-transparent bg-transparent focus:border-b-primary"
          :class="{
            'border-b-primary/30 hover:border-b-primary/60 text-primary text-center' : editing,
            'border-b-red' : errors.duration
          }"
        >
      </InputWrapper>
    </div>
  </div>
</template>
