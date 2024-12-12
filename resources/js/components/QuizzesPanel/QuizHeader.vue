<script setup lang="ts">
import { type Errors } from '@inertiajs/core'
import InputWrapper from '@/components/QuizzesPanel/InputWrapper.vue'
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
  <div class="flex flex-col gap-1 w-full px-2">
    <CrudInput
      v-model="quiz.title"
      name="title"
      :error="errors.title"
      :editing="editing"
    />

    <div
      class="flex gap-1 duration-200 min-h-6.5"
      :class="{ 'text-sm text-gray-600' : !selected }"
    >
      <InputWrapper
        label="Rozpoczęcie testu:"
        :hide-content="!quiz.scheduledAt && !editing"
        :error="errors.scheduled_at"
        :hide-error="!editing"
      >
        <b
          v-if="!editing"
          class="whitespace-nowrap"
        >
          {{ formatDate(quiz.scheduledAt) }}
        </b>

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
      <CrudInput
        v-model="quiz.duration"
        name="duration"
        label="Czas trwania testu (min):"
        :error="errors.duration"
        :editing="editing"
      />
    </div>
  </div>
</template>
