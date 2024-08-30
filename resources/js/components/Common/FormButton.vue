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
      class="px-3 font-semibold transition-colors duration-200 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600"
      :class="`${text ? 'text-black hover:text-primary-800' : 'bg-primary text-white hover:bg-primary-600  shadow-sm '} ${small ? 'rounded-md text-sm py-2' : 'rounded-lg py-3'} ${props.class}`"
    >
      <slot />
    </button>
  </form>
</template>
