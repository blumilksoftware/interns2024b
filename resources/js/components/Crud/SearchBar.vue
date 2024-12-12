<script setup lang="ts">
import { ref } from 'vue'
import { ChevronDownIcon, MagnifyingGlassIcon } from '@heroicons/vue/24/outline'
import { useDebounceFn } from '@vueuse/core'
import Dropdown from '@/components/Common/Dropdown.vue'

const props = defineProps<{ defaultValue?: string, modes?: Mode[] }>()
const text = ref(props.defaultValue)

const emit = defineEmits<{ search: [value:string|undefined, mode?:string] }>()
const input = ref<HTMLInputElement>()

const selectedMode = ref<Mode | undefined>(props.modes ? props.modes[0] : undefined)

function search() {
  emit('search', text.value, selectedMode.value?.name)
}

function handleKeyUp(e: KeyboardEvent) {
  if (e.key === 'Enter') {
    search()
  }
}

const handleInput = useDebounceFn(search, 500)

function pickMode(mode: Mode) {
  selectedMode.value = mode
  search()
}
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
    
    <Dropdown
      v-if="modes && modes.length > 1"
      pointer-position="left"
      :options="modes"
      @option-click="(option: any) => pickMode(option)"
    >
      <div class="flex gap-2 p-2 text-gray-800 items-center hover:bg-primary/5 hover:text-primary rounded-lg duration-200 whitespace-nowrap">
        <ChevronDownIcon class="size-3" />

        <span class="nowrap">
          {{ selectedMode?.text }}
        </span>
      </div>
    </Dropdown>

    <button
      title="Szukaj"
      class="flex gap-2 hover:bg-primary/5 hover:text-primary duration-200 p-2 rounded-lg opacity-50 hover:opacity-70"
      @click="search"
    >
      <MagnifyingGlassIcon class="size-5 stroke-2" />
    </button>
  </div>
</template>
