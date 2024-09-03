<script setup lang="ts">

import { useForm } from '@inertiajs/vue3'
import Button from "@/components/Common/Button.vue";

const form = useForm({})
const props = withDefaults(defineProps<{
  small?: boolean
  disabled?: boolean
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
  <form :class="`${form.processing ? 'cursor-wait' : ''} ${props.class}`" @submit.prevent="handleSubmit">
    <Button :disabled="disabled || form.processing" type="submit" :class="`${form.processing ? 'cursor-wait' : ''} ${props.class}`" :small="small" :text="text">
      <slot />
    </Button>
  </form>
</template>
