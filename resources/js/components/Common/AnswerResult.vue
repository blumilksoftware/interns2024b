<script setup lang="ts">
import { CheckIcon, XMarkIcon } from '@heroicons/vue/20/solid'

withDefaults(defineProps<{
    id?: string
    checked?: boolean
    bold?: boolean
    mode?: 'success' | 'error' | 'none'
  }>(), { mode: 'none', id: undefined })
</script>

<template>
  <label class="flex items-center text-sm text-black relative cursor-not-allowed" :class="{'font-semibold': bold }">
    <input
      :id
      type="radio"
      disabled="disabled"
      :checked
      :class="{'appearance-none': mode !== 'none'}"
      class="mr-2 p-2 border border-gray-600 rounded-full"
    >

    <div
      v-if="checked && mode !== 'none'"
      class="flex justify-center items-center p-2 border border-transparent absolute"
      :class="{
        'stroke-primary hover:stroke-primary-700': mode === 'none',
        'stroke-red hover:stroke-orange-700': mode === 'error',
        'stroke-green-700 hover:stroke-green-900': mode === 'success'
      }"
    >
      <XMarkIcon v-if="mode=='error'" class="size-3 absolute" />
      <CheckIcon v-else-if="mode=='success'" class="size-3 absolute" />
    </div>
    <slot />
  </label>
</template>

