<script setup lang="ts">
import { CheckIcon, XMarkIcon, StopCircleIcon } from '@heroicons/vue/20/solid'

const props = withDefaults(defineProps<{
    id?: string
    native?: boolean
    checked?: boolean
    class?: string
    mode?: 'v' | 'x' | 'dot'
    fill: 'gray' | 'primary' | 'red' | 'green'
  }>(), { mode: 'v', id: undefined, class: undefined })
</script>

<template>
  <label class="flex items-center text-sm text-black relative cursor-not-allowed"  :class="`${props.class}`">
    <input
      :id
      type="radio"
      disabled="disabled"
      :checked
      :class="`${native ? '' : 'appearance-none'} ${props.class}`"
      class="mr-2 p-2 border border-gray-600 rounded-full"
    >

    <div
      v-if="checked && !native"
      class="flex justify-center items-center p-2 border border-transparent absolute"
      :class="{
        'stroke-gray-500 hover:stroke-gray-700': fill === 'gray',
        'stroke-primary hover:stroke-primary-700': fill === 'primary',
        'stroke-red hover:stroke-orange-700': fill === 'red',
        'stroke-green-700 hover:stroke-green-900': fill === 'green'
      }"
    >
      <XMarkIcon v-if="mode=='x'" class="size-3 absolute" />
      <CheckIcon v-else-if="mode=='v'" class="size-3 absolute" />
      <StopCircleIcon v-else class="size-3 absolute" />
    </div>
    <slot />
  </label>
</template>

