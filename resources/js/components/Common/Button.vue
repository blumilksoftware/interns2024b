<script setup lang="ts">

defineProps<{
  small?: boolean
  disabled?: boolean
  text?: boolean
  type?: 'button' | 'submit' | 'reset' | undefined
}>()

const emit = defineEmits(['click'])
</script>

<template>
  <button
    v-if="!disabled"
    class="font-semibold transition-colors duration-200 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600"
    :class="{
      'rounded-md text-xs 2xs:text-sm py-2': small,
      'rounded-lg py-3': !small,
      'text-black hover:text-primary-800 p-0': text,
      'px-2 2xs:px-3 bg-primary text-white hover:bg-primary-600 shadow-sm': !text,
    }"
    :type
    @click="emit('click')"
  >
    <slot />
  </button>

  <button
    v-else
    disabled
    class="font-semibold text-gray-500 cursor-not-allowed"
    :class="{
      'rounded-md text-xs 2xs:text-sm py-2': small,
      'rounded-lg py-3': !small,
      'p-0': text,
      'border border-gray-500 font-semibold px-2 2xs:px-3': !text
    }"
    :type
  >
    <slot />
  </button>
</template>
