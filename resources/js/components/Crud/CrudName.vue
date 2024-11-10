<script setup lang="ts" generic="T extends { name: string }">
import InputWrapper from '@/components/QuizzesPanel/InputWrapper.vue'
import vDynamicInputWidth from '@/Helpers/vDynamicInputWidth'
import { type Errors } from '@inertiajs/core'

const item = defineModel<T>({ required: true })
defineProps<{ editing: boolean, errors: Errors }>()
</script>

<template>
  <InputWrapper
    :has-content="!!item.name || editing"
    :error="errors.name"
    :show-error="editing"
  >
    <input
      v-model="item.name"
      v-dynamic-input-width
      type="text"
      name="name"
      autocomplete="off"
      class="w-full outline-none font-bold border-b border-transparent duration-200 transition-colors text-lg bg-transparent focus:border-b-primary"
      :class="{
        'border-b-primary/30 duration-200 transition-colors hover:border-b-primary/60 text-primary' : editing,
        'border-b-red' : errors.name
      }"
      :disabled="!editing"
    >
  </InputWrapper>
</template>
