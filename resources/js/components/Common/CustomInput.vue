<script setup lang="ts">
import { ref, type VNode } from 'vue'
import { onClickOutside } from '@vueuse/core'

const slots = defineSlots<{ default: VNode }>()
const target = ref()
onClickOutside(target, ()=> isFocused.value = false)

withDefaults(defineProps<{
  label: string
  name: string
  error?: string
  type?: string
  required? : boolean
  placeholder? : string
  autocomplete?: string
}>(), {
  type: 'text',
  required: true,
  autocomplete: 'off',
  placeholder: '',
  error: '',
})

const isFocused = ref<boolean>(false)
const model = defineModel<string>()
</script>

<template>
  <label ref="target" class="block w-full text-sm font-medium leading-6 text-gray-900 duration-200">{{ label }}
    <div
      class=" w-full mt-2 duration-200 max-h-12 flex bg-white/30 rounded-lg border-2 border-primary/30 overflow-hidden px-3 gap-3"
      :class="{'border-3 border-primary/60':isFocused, 'border-red' : error}"
    >
      <input
        v-model="model"
        class="outline-none py-3 bg-transparent w-full"
        :name="name"
        :type="type"
        :required="required"
        :placeholder="placeholder"
        :autocomplete="autocomplete"
        @focus="isFocused=true"
        @blur="isFocused=false"
      >

      <div v-if="slots.default" class="flex flex-1 items-center justify-center stroke-primary/100">
        <slot />
      </div>
    </div>
    <div v-if="error" class="text-red">{{ error }}</div>
  </label>
</template>
