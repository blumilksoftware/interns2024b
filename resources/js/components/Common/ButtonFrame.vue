<script setup lang="ts">
import { type ButtonFrameProps } from '@/Types/ButtonFrameProps'

const slots = defineSlots()
const defaultSlot = slots.default()[0]
defineProps<ButtonFrameProps>()
</script>

<template>
  <div
    class="flex gap-2 transition-colors-opacity duration-200"
    :class="{
      'rounded-lg text-xs xs:text-sm': small,
      'rounded text-xs px-2': extraSmall,
      'rounded-xl': !small && !extraSmall,
      'focus:rounded-sm text-black hover:text-primary-800 p-0 font-semibold': text,
      'px-4 bg-primary text-white hover:bg-primary-950 py-2.5 font-bold': !text,
      'opacity-50 pointer-events-none': disabled,
      'pl-3': defaultSlot?.children?.at(0)?.type !== Symbol.for('v-txt'),
      'pr-3': defaultSlot?.children?.at(-1)?.type !== Symbol.for('v-txt'),
    }"
  >
    <slot />
  </div>
</template>
