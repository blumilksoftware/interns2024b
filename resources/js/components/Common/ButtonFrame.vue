<script setup lang="ts">
import { type ButtonFrameProps } from '@/Types/ButtonFrameProps'

const slots = defineSlots()
defineProps<ButtonFrameProps>()

function childIsNotText(index: number){
  return slots.default()[0]?.children?.at(index)?.type !== Symbol.for('v-txt')
}
</script>

<template>
  <div
    class="flex justify-center gap-2 transition-colors-opacity duration-200"
    :class="{
      'rounded-lg size-6 xs:text-sm items-center': icon,
      'text-xs xs:text-sm': small,
      'text-xs px-2': extraSmall,
      'py-3 max-w-96 w-full': large,
      'focus:rounded-xl text-black hover:text-primary-bright p-0 font-semibold': text,
      'bg-primary text-white hover:bg-primary-dark py-2.5 font-bold': !text,
      'rounded-xl px-4 py-2.5': !text && !icon,
      'opacity-50 pointer-events-none': disabled,
      'pl-3': childIsNotText(0),
      'pr-3': childIsNotText(-1),
    }"
  >
    <slot />
  </div>
</template>
