<script lang="ts" setup>
import MessageBox from '@/components/Common/MessageBox.vue'
import { ExclamationTriangleIcon } from '@heroicons/vue/24/outline'

defineProps<{
  open: boolean
  hideCancelButton?: boolean
}>()

const emit = defineEmits(['close'])
</script>

<template>
  <MessageBox
    :open="open"
    @close="emit('close')"
  >
    <template #message>
      <div class="flex gap-5">
        <div class="bg-red/10 p-5 rounded-full h-fit">
          <ExclamationTriangleIcon class="size-6 text-red" />
        </div>

        <div>
          <slot name="message" />
        </div>
      </div>
    </template>

    <template #buttons>
      <button
        v-if="!hideCancelButton"
        class="px-2 font-bold"
        @click="emit('close')"
      >
        Anuluj
      </button>

      <slot name="buttons" />
    </template>
  </MessageBox>
</template>
