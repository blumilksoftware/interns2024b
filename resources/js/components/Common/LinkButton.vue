<script setup lang="ts">
import ButtonFrame from './ButtonFrame.vue'
import { type ButtonFrameProps } from '@/Types/ButtonFrameProps'

defineProps<{
  href: string
  buttonClass?: string
} & ButtonFrameProps>()
</script>

<template>
  <a v-if="!disabled" :href="href">
    <ButtonFrame
      :class="buttonClass"
      :disabled :small :extra-small :text :icon
    >
      <slot />
    </ButtonFrame>
  </a>

  <ButtonFrame v-if="disabled" :class="buttonClass" :disabled :small :extra-small :text :icon>
    <slot />
  </ButtonFrame>
</template>
