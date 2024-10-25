<script setup lang="ts">
import ButtonFrame from '@/components/Common/ButtonFrame.vue'
import { type ButtonFrameProps } from '@/Types/ButtonFrameProps'

defineProps<{ type?: 'button' | 'submit' | 'reset' | undefined } & ButtonFrameProps>()

const emit = defineEmits(['click'])
</script>

<template>
  <button :disabled :type="type" @click="emit('click')">
    <ButtonFrame :disabled :small :extra-small :text>
      <slot />
    </ButtonFrame>
  </button>
</template>
