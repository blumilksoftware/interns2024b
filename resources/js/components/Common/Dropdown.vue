<script setup lang="ts">
import { type Option } from '@/Types/Option'
import { ref } from 'vue'
import { onClickOutside } from '@vueuse/core'

const target = ref(null)

onClickOutside(target, () => isVisible.value=false)
defineProps<{ options: Option[] }>()
const isVisible = ref<boolean>(false)
const emit = defineEmits<{ optionClick: [id: number | string] }>()

function pick(option: Option) {
  option.action()
  emit('optionClick', option.key)
}

</script>

<template>
  <div class="flex flex-col items-center">
    <button @click="isVisible=!isVisible">
      <slot />
    </button>
    <div
      v-if="isVisible"
      ref="target"
      class="absolute mt-10 z-50 w-fit rounded-lg bg-white border shadow border-primary/30 focus:outline-none" role="menu"
      aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1"
    >
      <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownDefaultButton">
        <li v-for="option of options" :key="option.key">
          <a id="menu-item-0" href="#"
             class="truncate block px-4 py-2 text-sm text-gray-700 hover:bg-primary hover:text-white hover:drop-shadow-2xl transition-all" role="menuitem" tabindex="-1" @click="pick(option)"
          >{{ option.text }}</a>
        </li>
      </ul>
    </div>
  </div>
</template>
