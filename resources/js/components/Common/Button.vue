<script setup lang="ts">

defineProps<{
  small?: boolean
  extraSmall?: boolean
  disabled?: boolean
  text?: boolean
  type?: 'button' | 'submit' | 'reset' | undefined
}>()

const emit = defineEmits(['click'])
</script>

<template>
  <button
    class="font-semibold transition-colors duration-200"
    :class="{
      'rounded-lg text-xs xs:text-sm': small,
      'rounded text-xs px-2': extraSmall,
      'rounded-xl': !small && !extraSmall,
      'focus:rounded-sm text-black hover:text-primary-800 p-0': text,
      'px-4 bg-primary text-white hover:bg-primary-950 py-2': !text,
      'opacity-50 pointer-events-none': disabled
    }"
    :type="type"
    :disabled="disabled"
    @click="emit('click')"
  >
    <slot />
  </button>
</template>
