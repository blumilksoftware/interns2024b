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
      'rounded-lg size-6 xs:text-sm flex items-center justify-center': icon,
      'text-xs xs:text-sm': small,
      'text-xs px-2': extraSmall,
      'focus:rounded-xl text-black hover:text-primary-800 p-0 font-semibold': text,
      'bg-primary text-white hover:bg-primary-950 font-bold': !text,
      'rounded-xl px-4 py-2.5 ': !text && !icon,
      'opacity-50 pointer-events-none': disabled,
      'pl-3': !icon && defaultSlot?.children?.at(0)?.type !== Symbol.for('v-txt'),
      'pr-3': !icon && defaultSlot?.children?.at(-1)?.type !== Symbol.for('v-txt'),
    }"
  >
    <slot />
  </div>
</template>
