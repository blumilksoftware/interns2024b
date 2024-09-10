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
  if (props.type === 'number') return content + ' min'
  if (props.type === 'datetime-local') return dayjs(content).format('DD.MM.YYYY HH:mm')
  return content
}
</script>

<template>
  <div class="flex flex-col gap-1">
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
      :class="{'font-normal':fontNormal, 'ring-red':error }"
      class="w-full py-1.5 outline-none font-bold px-3 rounded-md ring-1 ring-primary/30 focus:ring-2 focus:ring-primary/50 bg-white/50"
      autocomplete="off"
    >
    <span v-if="isEditing && error" :title="error" class="text-red text-sm truncate max-w-xs">{{ error }}</span>
  </div>
</template>
