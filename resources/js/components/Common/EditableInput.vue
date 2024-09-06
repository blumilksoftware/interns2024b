<script setup lang="ts">
import dayjs from 'dayjs'

const props = defineProps<{
  fontNormal? :boolean
  type  :string
  isEditing : boolean
  min?: string
  error?: string
}>()
const model = defineModel<number | string>()

function formatOutput(content: number | string | undefined) {
  if (content === null || content === undefined) return 'brak'
  if (props.type === 'number') return content + ' minut'
  if (props.type === 'datetime-local') return dayjs(content).format('DD.MM.YYYY HH:mm')
  return content
}
</script>

<template>
  <div>
    <div
      v-if="!isEditing"
      :class="{'font-normal':fontNormal }"
      class="py-1.5 font-bold"
    >
      {{ formatOutput(model) }}
    </div>
    <input
      v-else
      v-model="model"
      :min="min"
      :type="type"
      :class="{'font-normal':fontNormal }"
      class="outline-none font-bold flex px-3 py-2 min-h-8 rounded-md size-full ring-1 ring-primary/30 focus:ring-2 focus:ring-primary/50 bg-white/50"
      autocomplete="off"
    >
    <span v-if="isEditing" class="text-red py-1.5 whitespace-nowrap">{{ error }}</span>
  </div>
</template>
