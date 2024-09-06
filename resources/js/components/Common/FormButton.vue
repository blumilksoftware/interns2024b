<script setup lang="ts">

import { router } from '@inertiajs/vue3'
import {ref} from 'vue'

const props = withDefaults(defineProps<{
  small?: boolean
  disabled?: boolean
  text?: boolean
  class?: string
  href: string
  method?: 'post' | 'get' | 'put' | 'patch' | 'delete'
  data?: any
  preserveState?: boolean
  preserveScroll?: boolean
  only?: string[]
}>(), { method: 'get', class: '', options: undefined, data: undefined, only: undefined })

const processing = ref(false)
const emit = defineEmits(['click', 'finish'])

function handleSubmit() {
  processing.value = true
  emit('click')

  router[props.method](props.href, props.data, {
    onSuccess: () => { processing.value = false; emit('finish')},
    only: props.only,
    preserveState: props.preserveState,
    preserveScroll: props.preserveScroll,
  })
}
</script>

<template>
  <form :class="props.class" @submit.prevent="handleSubmit">
    <button
      v-if="!disabled && !processing"
      type="submit"
      class="font-semibold transition-colors duration-200 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600"
      :class="`${small ? 'rounded-md text-xs 2xs:text-sm py-2' : 'rounded-lg py-3'} ${text ? 'text-black hover:text-primary-800 px-0 py-0' : 'px-2 2xs:px-3 bg-primary text-white hover:bg-primary-600 shadow-sm'} ${props.class}`"
    >
      <slot />
    </button>

    <button
      v-else
      disabled
      type="submit"
      class="font-semibold text-gray-500 cursor-not-allowed"
      :class="`${small ? 'rounded-md text-xs 2xs:text-sm py-2' : 'rounded-lg py-3'} ${text ? 'px-0 py-0' : 'border border-gray-500 font-semibold px-2 2xs:px-3 '} ${props.class}`"
    >
      <slot />
    </button>
  </form>
</template>
