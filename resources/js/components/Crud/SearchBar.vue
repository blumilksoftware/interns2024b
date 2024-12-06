<script setup lang="ts">
import { ref } from 'vue'
import { ChevronDownIcon, MagnifyingGlassIcon } from '@heroicons/vue/24/outline'
import { useDebounceFn } from '@vueuse/core'
import Dropdown from '@/components/Common/Dropdown.vue'

const props = defineProps<{ defaultValue?: string, options?: Option[] }>()
const text = ref(props.defaultValue)

const emit = defineEmits<{ search: [value:string|undefined] }>()
const input = ref<HTMLInputElement>()

const selectedOption = ref<Option | undefined>(props.options ? props.options[0] : undefined)

function handleKeyUp(e: KeyboardEvent) {
  if (e.key === 'Enter') {
    emit('search', text.value)
  }
}

const handleInput = useDebounceFn(() => {
  emit('search', text.value)
}, 500)
</script>

<template>
  <div class="flex items-center bg-white/70 text-primary duration-200 p-1 rounded-lg w-full border border-primary/30 h-fit text-sm">
    <input
      ref="input"
      v-model="text"
      type="text"
      autocomplete="off"
      placeholder="Szukaj"
      class="pl-2 transition-none size-full outline-none text-primary placeholder:text-primary/60 text-left rounded-lg bg-transparent"
      @keyup="handleKeyUp"
      @input="handleInput"
    >
    
    <Dropdown v-if="options && options.length > 1" pointer-position="left" :options @option-click="option=>selectedOption = option">
      <div class="flex gap-1 text-gray-800 items-center hover:bg-primary/5 hover:text-primary p-2 pr-1 rounded-lg duration-200">
        {{ selectedOption?.text }} <ChevronDownIcon class="size-3" />
      </div>
    </Dropdown>

    <button
      title="Szukaj"
      class="flex gap-2 hover:bg-primary/5 hover:text-primary duration-200 p-2 rounded-lg opacity-50 hover:opacity-70"
      @click="emit('search', text)"
    >
      <MagnifyingGlassIcon class="size-5 stroke-2" />
    </button>
  </div>
</template>
