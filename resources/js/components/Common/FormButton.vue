<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'

const form = useForm({})

const props = withDefaults(defineProps<{
  small?: boolean
  text?: boolean
  class?: string
  href: string
  method?: 'post' | 'get' | 'put' | 'patch' | 'delete'
  options?: any
}>(), { method: 'get', class: '', options: {}})

function handleSubmit() {
  form[props.method](props.href, props.options)
}
</script>

<template>
  <form :class="props.class" @submit.prevent="handleSubmit">
    <button
      type="submit"
      class="font-semibold transition-colors duration-200 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600"
      :class="`${small ? 'rounded-md text-sm py-2' : 'rounded-lg py-3'} ${text ? 'text-black hover:text-primary-800 px-0 py-0' : 'px-3 bg-primary text-white hover:bg-primary-600 shadow-sm'} ${props.class}`"
    >
      <slot />
    </button>
  </form>
</template>
