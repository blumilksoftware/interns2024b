<script lang="ts" setup generic="T extends Option">
import { ref, defineProps, computed } from 'vue'
import { onClickOutside } from '@vueuse/core'
import { nanoid } from 'nanoid'

const id = ref(nanoid())
const target = ref()
onClickOutside(target, () => isFocused.value = false)
const isFocused = ref(false)
const searchQuery = ref('')
const props = defineProps<{
  options:T[]
  label:string
  ariaLabel?:string
  error?:string
}>()
const emit = defineEmits<{ change: [option:T] }>()
const selectedOption = ref<T>()
const optionsRef = computed(() =>
  props.options.filter(option =>
    option.text.toLowerCase().includes(searchQuery.value) ||
    option.title?.toLowerCase().includes(searchQuery.value),
  ),
)

function onOptionClick(option:T) {
  selectedOption.value = option
  isFocused.value = false
  emit('change', option)
}
</script>

<template>
  <div class="block w-full text-sm font-medium leading-6 text-gray-900 duration-200">
    <label :for="id">{{ label }}</label>
    <div
      ref="target"
      class="mt-2 font-medium text-sm leading-6 text-gray-900 overflow-hidden duration-200 max-h-12 flex flex-col bg-white/30 placeholder:text-gray-400 rounded-lg ring-2 ring-primary/30 ring-inset"
      :class="{'scale-y-100 max-h-80': isFocused, 'ring-red' : error }"
    >
      <div
        class="flex h-inherit items-center justify-center duration-200 rounded-lg"
        :class="{ 'ring-inset ring-2 ring-[#7e76b8]' : isFocused, 'ring-red' : error }"
      >
        <div class="h-full items-center justify-center px-3">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5 text-primary/30" :class="{'!text-primary/60': isFocused}">
            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
          </svg>
        </div>

        <input
          :id="id"
          class="outline-none py-3 bg-transparent w-full text-gray-900"
          autocomplete="off"
          name="search"
          type="text"
          required
          :aria-label="ariaLabel"
          :value="isFocused ? searchQuery : selectedOption?.text"
          :class="{'cursor-pointer' : !isFocused}"
          :placeholder="selectedOption?.text"
          @input="(e:any)=>searchQuery=e.currentTarget.value"
          @focus="isFocused=true"
        >
      </div>

      <Transition>
        <div v-show="isFocused" class="m-0.5 mt-0 py-2 overflow-auto">
          <div v-if="optionsRef.length>0">
            <button
              v-for="obj in optionsRef"
              :key="obj.key"
              class="cursor-pointer block px-4 py-2 hover:bg-primary/10 outline-none focus:bg-primary/10 text-[0.9rem] w-full text-left"
              @click="onOptionClick(obj)"
              @focus="isFocused=true"
            >
              <b v-if="obj.title">{{ obj.title.toUpperCase() }}</b>
              <p>{{ obj.text }}</p>
            </button>
          </div>
          <span v-else class="block px-4 py-2 text-sm">
            Nie znaleziono szkoły
          </span>
        </div>
      </Transition>
    </div>
    <div v-if="error" class="text-red">{{ error }}</div>
  </div>
</template>
