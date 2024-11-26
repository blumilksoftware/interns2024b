<script setup lang="ts">
import { computed } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps<{ theme: 'theme-witelon' | 'theme-tauron' }>()
const isThemeTauron = computed(() => props.theme === 'theme-tauron')

function toggleTheme(checked?:boolean) {
  document.documentElement.classList.remove(checked ? 'theme-witelon' : 'theme-tauron')
  document.documentElement.classList.add(checked ? 'theme-tauron' : 'theme-witelon')
  router.patch('/profile/theme', { theme: checked ? 'theme-tauron' : 'theme-witelon' })
}
</script>

<template>
  <label class="inline-flex items-center cursor-pointer">
    <input type="checkbox" class="sr-only peer" :checked="isThemeTauron" @click="(event: any) => toggleTheme(event.currentTarget.checked)">
    <div class="
      relative
      w-11
      h-6
      bg-primary
      peer-focus:outline-none
      peer-focus:ring-2
      rounded-full
      
      after:bg-white
      after:content-['']
      after:start-0.5
      after:absolute after:top-0.5
      after:border
      after:rounded-full
      after:size-5
      peer-checked:after:translate-x-full
      rtl:peer-checked:after:-translate-x-full
      after:transition-all
      "
    />
    <span class="ms-3 text-sm font-medium text-gray-500">Zmień skórkę</span>
  </label>
</template>
