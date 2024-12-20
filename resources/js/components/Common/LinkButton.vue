<script setup lang="ts">
import ButtonFrame from './ButtonFrame.vue'
import { type ButtonFrameProps } from '@/Types/ButtonFrameProps'

defineProps<{
  href: string
  buttonClass?: string
} & ButtonFrameProps>()
</script>

<template>
  <a
    :disabled
    :href="href"
    :class="{ 'w-full flex justify-center' : large, 'pointer-events-none': disabled }"
  >
    <ButtonFrame
      :class="buttonClass"
      :disabled
      :small
      :extra-small
      :large
      :text
      :icon
    >
      <slot />
    </ButtonFrame>
  </a>
</template>
