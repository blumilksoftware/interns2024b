<script setup lang="ts">
import CustomRadio from '@/components/Common/CustomRadio.vue'

defineProps<{
  answer: { id: number, text: string }
  timeout: boolean
  checked: boolean
}>()
const emit = defineEmits<{ change: [answerId: number] }>()
</script>

<template>
  <label
    class="flex gap-2.5 text-gray-900"
    :class="timeout ? 'cursor-not-allowed' : 'cursor-pointer'"
    :title="timeout ? 'Czas przewidziany na ten test dobiegł końca' : undefined"
  >
    <CustomRadio
      class="mt-1 text-primary"
      :class="timeout ? 'cursor-not-allowed' : 'cursor-pointer'"
      :disabled="timeout"
      :checked
      @change="emit('change', answer.id)"
    />
    {{ answer.text }}
  </label>
</template>
