<script setup lang="ts">
import { type ButtonFrameProps } from '@/Types/ButtonFrameProps'

defineProps<ButtonFrameProps>()
</script>

<template>
  <div
    class="flex gap-2 transition-colors-opacity duration-200"
    :class="{
      'rounded-lg text-xs xs:text-sm': small,
      'rounded text-xs px-2': extraSmall,
      'rounded-xl': !small && !extraSmall,
      'focus:rounded-sm text-black hover:text-primary-bright p-0 font-semibold': text,
      'px-4 bg-primary text-white hover:bg-primary-dark py-2.5 font-bold': !text,
      'opacity-50 pointer-events-none': disabled,
    }"
  >
    <slot />
  </div>
</template>
