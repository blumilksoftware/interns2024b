<script setup lang="ts">
import { ref } from 'vue'


withDefaults(defineProps< {
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
  <label class="block w-full text-sm font-medium leading-6 text-gray-900" :class="{'text-red': error}">{{ label }}
    <div class="mt-2">
      <div :class="{'scale-y-100 max-h-80': isFocused}" class="w-full font-medium text-sm leading-6 text-gray-900 overflow-hidden duration-200 max-h-12 flex flex-col mt-2 bg-white/30 placeholder:text-gray-400 rounded-[.5rem] ring-2 ring-primary/30 ring-inset">
        <div class="flex pl-3 h-inherit items-center justify-center duration-200 rounded-[.5rem]" :class="{'ring-inset ring ring-primary':isFocused}">
          <input
            v-model="model"
            class="outline-none py-3 bg-transparent w-full text-gray-900"
            :name="name"
            :type="type"
            :required="required"
            :placeholder="placeholder"
            :autocomplete="autocomplete"
            @focus="isFocused=true"
            @blur="isFocused=false"
          >

          <div v-if="$slots.default" class="flex h-full items-center justify-center px-3 stroke-primary/100">
            <slot />
          </div>
        </div>
      </div>
      <div v-if="error" class="text-red">{{ error }}</div>
    </div>
  </label>
</template>
