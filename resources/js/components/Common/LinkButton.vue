<script setup lang="ts">
import ButtonFrame from './ButtonFrame.vue'
import { type ButtonFrameProps } from '@/Types/ButtonFrameProps'

defineProps<{
  href: string
  buttonClass?: string
} & ButtonFrameProps>()
</script>

<template>
  <a :href="href" :class="{ 'w-full flex justify-center' : large }">
    <ButtonFrame
      :class="buttonClass"
      :disabled :small :extra-small :large :text
    >
      <slot />
    </ButtonFrame>
  </a>
</template>
