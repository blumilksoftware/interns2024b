<script setup lang="ts">
import {ref} from 'vue'
import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline'
import { useDebounceFn } from '@vueuse/core'

const props = defineProps<{ defaultValue?: string }>()
const text = ref(props.defaultValue)
const emit = defineEmits<{ search: [value:string|undefined] }>()
const input = ref<HTMLInputElement>()

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
  <div class="flex gap-1 bg-white/70 text-primary duration-200 p-1 rounded-xl w-full border border-primary/5">
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

    <div title="Szukaj" class="flex gap-2 hover:bg-primary/5 hover:text-primary duration-200 p-2 rounded-lg cursor-pointer" @click="emit('search', text);">
      <MagnifyingGlassIcon class="size-5 stroke-2" />
    </div>
  </div>
</template>
