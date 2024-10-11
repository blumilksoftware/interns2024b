<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import {ref} from 'vue'
import Button from '@/components/Common/Button.vue'

const props = withDefaults(defineProps<{
  small?: boolean
  extraSmall?: boolean
  disabled?: boolean
  text?: boolean
  buttonClass?: string
  href: string
  method?: 'post' | 'get' | 'put' | 'patch' | 'delete'
  data?: any
  preserveState?: boolean
  preserveScroll?: boolean
  only?: string[]
}>(), { method: 'get', class: '', options: undefined, data: undefined, only: undefined, buttonClass: undefined })

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
  <form :class="{'cursor-wait' : processing}" @submit.prevent="handleSubmit">
    <Button :disabled="disabled || processing" type="submit" :class="`${processing ? 'cursor-wait' : ''} ${props.buttonClass}`" :extra-small="extraSmall" :small="small" :text="text">
      <slot />
    </Button>
  </form>
</template>
