<script setup lang="ts">
import { type Errors } from '@inertiajs/core'
import CustomDatepicker from '@/components/Common/CustomDatepicker.vue'
import { formatDate } from '@/Helpers/Format'
import CrudInput from '@/components/Crud/CrudInput.vue'
import CrudSelect from '@/components/Crud/CrudSelect.vue'

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
      <CrudSelect
        label="Widoczność: "
        :editing="editing"
        :value="quiz.isPublic?.toString() ?? 'false'"
        :error="errors.is_public"
        :items="[
          { key: 'false', text: 'Dostępny tylko dla zaproszonych' },
          { key: 'true', text: 'Dostępny dla wszystkich' }
        ]"
        @change="quiz.isPublic = $event === 'true'"
      />
    </div>
  </div>
</template>
