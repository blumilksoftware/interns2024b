<script setup lang="ts">
import { ref } from 'vue'
import { onClickOutside } from '@vueuse/core'
import type Option from '@/Types/Option'

defineProps<{ options:Option[], classBtn?:string, pointerPosition?:'left'|'right'|'center' }>()
const emit = defineEmits<{ optionClick: [option:Option] }>()
const isVisible = ref<boolean>(false)
const target = ref(null)

onClickOutside(target, () => isVisible.value=false)

function pick(option: Option) {
  if (option.action) option.action()
  emit('optionClick', option)
}
</script>

<template>
  <div ref="target">
    <button :class="classBtn" @click="isVisible=!isVisible">
      <slot />
    </button>
    <Transition>
      <div
        v-if="isVisible"
        class="absolute z-50 w-fit rounded-lg bg-white/70 backdrop-blur-md outline-none shadow border border-primary/30 mt-1" role="menu"
        aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1"
      >
        <div
          v-if="pointerPosition"
          class="absolute top-0"
          :class="{ 
            'left-5': pointerPosition==='left',
            'right-5': pointerPosition==='right',
            'left-1/2': pointerPosition==='center'
          }"
        >
          <div class="absolute -translate-x-1/2 -translate-y-full border-transparent border-b-primary/30 border-9" />
          <div class="absolute -translate-x-1/2 -translate-y-full border-transparent border-b-white border-8" />
        </div>
        <ul class="py-0.5 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownDefaultButton">
          <li v-for="option of options" :key="option.key">
            <a 
              id="menu-item-0" href="#"
              class="truncate block py-2 px-3 m-1 text-sm text-gray-700 rounded-lg hover-focus:bg-primary/5 hover-focus:text-primary transition-colors outline-none"
              role="menuitem"
              @click="pick(option)"
            >
              {{ option.text }}
            </a>
          </li>
        </ul>
      </div>
    </Transition>
  </div>
</template>
