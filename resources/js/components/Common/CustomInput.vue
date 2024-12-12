<script setup lang="ts">
import { ref, type VNode } from 'vue'
import InputWrapper from '@/components/QuizzesPanel/InputWrapper.vue'
import vClickOutside from '@/Helpers/vClickOutside'

const slots = defineSlots<{ default: VNode, left: VNode }>()

withDefaults(
  defineProps<{
    error?: string
    label?: string
    ariaLabel?: string
    name: string
    type?: string
    required?: boolean
    placeholder?: string
    autocomplete?: string
  }>(), 
  {
    type: 'text',
    required: true,
    autocomplete: 'off',

    value: undefined,
    error: undefined,
    label: undefined,
    ariaLabel: undefined,
    placeholder: undefined,
  },
)
const emit = defineEmits<{ input: [value:string], focus: [] }>()
const model = defineModel<string>()

const isFocused = ref<boolean>(false)
</script>

<template>
  <InputWrapper
    v-click-outside="() => isFocused = false"
    class="text-sm font-medium leading-6 text-gray-900 duration-200"
    wrapper-class="gap-2"
    :label="label"
    :error="error"
    column
  >
    <div
      class="w-full duration-200 max-h-12 flex bg-white/30 rounded-lg border-2 border-primary/30 overflow-hidden px-3 gap-3"
      :class="{'border-3 border-primary/60':isFocused, 'border-red' : error}"
    >
      <div
        v-if="slots.left"
        class="flex flex-1 items-center justify-center text-primary"
      >
        <slot name="left" />
      </div>

      <input
        v-model="model"
        class="outline-none py-3 bg-transparent w-full"
        :aria-label="ariaLabel"
        :name="name"
        :type="type"
        :required="required"
        :placeholder="placeholder"
        :autocomplete="autocomplete"
        @input="({ currentTarget }) => emit('input', (currentTarget as any).value)"
        @focus="isFocused=true; emit('focus')"
        @blur="isFocused=false"
      >

      <div
        v-if="slots.default"
        class="flex flex-1 items-center justify-center text-primary"
      >
        <slot />
      </div>
    </div>
  </InputWrapper>
</template>
