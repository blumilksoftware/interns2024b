<script setup lang="ts" generic="T">
import InputWrapper from '@/components/QuizzesPanel/InputWrapper.vue'
import vDynamicInputWidth from '@/Helpers/vDynamicInputWidth'

const value = defineModel<T>({ required: true })

defineProps<{
  editing: boolean
  label?: string
  name: string
  error?: string
  password?: boolean
}>()
</script>

<template>
  <InputWrapper
    :error="error"
    :hide-error="!editing"
    :hide-content="!value && !editing"
    :label="label"
    :class="{ 'hidden': !value && !editing }"
  >
    <input
      v-model="value"
      v-dynamic-input-width
      :type="password ? 'password' : 'text'"
      :name="name"
      autocomplete="off"
      class="text-md transition-none h-fit w-full outline-none font-bold border-b border-transparent bg-transparent focus:border-b-primary"
      :class="{
        'border-b-primary/30 hover:border-b-primary/60 text-primary text-center' : editing,
        'border-b-red' : !!error,
      }"
      :disabled="!editing"
    >
  </InputWrapper>
</template>
