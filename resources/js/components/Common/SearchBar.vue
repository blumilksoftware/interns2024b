<script setup lang="ts">
import {ref} from 'vue'
import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline'

const props = defineProps<{ defaultValue?: string }>()
const text = ref(props.defaultValue)
const emit = defineEmits<{ search: [value:string|undefined] }>()
const input = ref<HTMLInputElement>()

function handleKeyUp(e: KeyboardEvent) {
  if (e.key === 'Enter') {
    emit('search', text.value )
  }
}
</script>

<template>
  <div class="flex items-center text-gray-400 gap-1 rounded-xl h-10 bg-gray-100">
    <input
      ref="input"
      v-model="text"
      type="text"
      autocomplete="off"
      placeholder="Szukaj"
      class="pl-2 transition-none size-full outline-none text-primary text-left rounded-xl bg-transparent"
      @keyup="handleKeyUp"
    >

    <div title="Szukaj" class="flex gap-2 hover:bg-primary/5 hover:text-primary duration-200 p-2 rounded-xl" @click="emit('search', text);">
      <MagnifyingGlassIcon class="size-5" />
    </div>
  </div>
</template>
