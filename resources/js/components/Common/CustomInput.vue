<script setup lang="ts">
import { ref } from 'vue'
import { onClickOutside } from '@vueuse/core'

const target =  ref(null)
onClickOutside(target,()=>isFocused.value=false)

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
  <label ref="target" class="block w-full text-sm font-medium leading-6 text-gray-900 duration-200" :class="{'text-red': error}">{{ label }}
    <div
      class="
        w-full mt-2 duration-200 max-h-12 flex bg-white/30 rounded-lg ring-2 ring-primary/30 ring-inset pl-3"
      :class="{'ring-[0.2rem] ring-primary/100':isFocused}"
    >
      <input
        v-model="model"
        class="outline-none py-3 bg-transparent w-full text-gray-900"
        :name="name"
        :type="type"
        :required="required"
        :placeholder="placeholder"
        :autocomplete="autocomplete"
        @focus="isFocused=true"
      >

      <div v-if="$slots.default" class="flex flex-1 items-center justify-center px-3 stroke-primary/100">
        <slot />
      </div>
    </div>
    <div v-if="error" class="text-red">{{ error }}</div>
  </label>
</template>
