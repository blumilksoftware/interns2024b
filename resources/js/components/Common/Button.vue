<script setup lang="ts">

const props = defineProps<{
  small?: boolean
  disabled?: boolean
  text?: boolean
  class?: string
  type?: 'button' | 'submit' | 'reset' | undefined
}>()

const emit = defineEmits(['click']);
</script>

<template>
    <button
      v-if="!disabled"
      class="font-semibold transition-colors duration-200 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600"
      :class="`${small ? 'rounded-md text-xs 2xs:text-sm py-2' : 'rounded-lg py-3'} ${text ? 'text-black hover:text-primary-800 px-0 py-0' : 'px-2 2xs:px-3 bg-primary text-white hover:bg-primary-600 shadow-sm'} ${props.class}`"
      :type
      @click="emit('click')"
    >
      <slot />
    </button>

    <button
      v-else
      disabled
      class="font-semibold text-gray-500 cursor-not-allowed"
      :class="`${small ? 'rounded-md text-xs 2xs:text-sm py-2' : 'rounded-lg py-3'} ${text ? 'px-0 py-0' : 'border border-gray-500 font-semibold px-2 2xs:px-3 '} ${props.class}`"
      :type
    >
      <slot />
    </button>
</template>
