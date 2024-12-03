<script setup lang="ts">
import vDynamicInputWidth from '@/Helpers/vDynamicInputWidth'
import {nextTick} from 'vue'

const zipCode = defineModel<string>({ required: true })

defineProps<{
  editing: boolean
  name: string
  error?: string
}>()

function handleInput() {
  nextTick(() => {
    let value = zipCode.value.replace(/[^0-9-]/g, '')

    if (value.length > 2 && value[2] !== '-') {
      value = value.slice(0, 2) + '-' + value.slice(2).replace('-', '')
    }

    zipCode.value = value.substring(0, 6)
  })
}
</script>

<template>
  <input
    v-model="zipCode"
    v-dynamic-input-width
    type="text"
    :name="name"
    autocomplete="off"
    class="text-md transition-none h-fit w-full outline-none font-bold border-b border-transparent bg-transparent focus:border-b-primary"
    :class="{
      'border-b-primary/30 hover:border-b-primary/60 text-primary text-center' : editing,
      'border-b-red' : !!error,
    }"
    :disabled="!editing"
    @input="handleInput"
  >
</template>

