<script lang="ts" setup generic="T extends Option">

import { ref, defineProps, computed} from 'vue'
import {type Option} from '@/Types/Option'
import { onClickOutside } from '@vueuse/core'

const target = ref()
onClickOutside(target,()=>isFocused.value=false)

const isFocused = ref(false)
const searchQuery = ref('')
const props = defineProps<{
  options: T[]
  label : string
  error: string
}>()

const selectedOption = ref<T>()
const options = computed(()=>props.options.filter(option=>option !== selectedOption.value))

const emit = defineEmits<{change:[option: T]}>()

const onOptionClick = (option:T)=>{
  selectedOption.value = option
  isFocused.value = false
  emit('change', option)
}

</script>

<template>
  <label ref="target" class="block w-full text-sm font-medium leading-6 text-gray-900 duration-200">
    {{ label }}
    <div 
      class="mt-2 font-medium text-sm leading-6 text-gray-900 overflow-hidden duration-200 max-h-12 flex flex-col bg-white/30 placeholder:text-gray-400 rounded-[.5rem] ring-2 ring-primary/30 ring-inset"
      :class="{'scale-y-100 max-h-80': isFocused}"
    >
      <div class="flex h-inherit items-center justify-center duration-200 rounded-[.5rem]" :class="{'ring-inset ring-2 ring-[#7e76b8]':isFocused}">
        <div class="h-full items-center justify-center px-3">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5 stroke-primary/30" :class="{'!stroke-primary/60': isFocused}">
            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
          </svg>
        </div>

        <input
          class="outline-none py-3 bg-transparent w-full text-gray-900"
          autocomplete="off"
          name="search"
          type="text"
          required
          :value="isFocused ? searchQuery : selectedOption?.text"
          :class="{'cursor-pointer' : !isFocused}"
          :placeholder="selectedOption?.text"
          @input="(e:any)=>searchQuery=e.currentTarget.value"
          @focus="isFocused=true"
        >
      </div>

      <Transition>
        <div v-show="true" class="m-0.5 mt-0 py-2 overflow-auto">
          <div v-if="options.length>0">
            <div 
              v-for="obj in options"
              :key="obj.key"
              class="cursor-pointer block px-4 py-2 hover:bg-primary/10 text-[0.9rem]"
              @mousedown="onOptionClick(obj)"
            >
              <b v-if="obj.title">{{ obj.title.toUpperCase() }}</b>
              <p>{{ obj.text }}</p>
            </div>
          </div>
          <span v-else class="block px-4 py-2 text-sm">
            Nie znaleziono szkoły
          </span>
        </div>
      </Transition>
    </div>
    <div v-if="error" class="text-red">{{ error }}</div>
  </label>
</template>
