<script setup lang="ts">
import { computed } from 'vue'

const isThemeTauron = computed(() => localStorage.getItem('theme') !== 'theme-witelon')

function toggleTheme(checked?: boolean) {
  document.documentElement.classList.remove(checked ? 'theme-witelon' : 'theme-tauron')
  document.documentElement.classList.add(checked ? 'theme-tauron' : 'theme-witelon')
  localStorage.setItem('theme', checked ? 'theme-tauron' : 'theme-witelon')
}
</script>

<template>
  <label class="inline-flex items-center cursor-pointer">
    <input
      type="checkbox"
      class="sr-only peer"
      :checked="isThemeTauron"
      @click="(event: any) => toggleTheme(event.currentTarget.checked)"
    >

    <div
      class="
        relative w-11 h-6 bg-primary peer-focus:outline-none peer-focus:ring-2 rounded-full duration-200
        after:bg-white after:content-[''] after:start-0.5 after:absolute after:top-0.5 after:border after:transition-all
        after:rounded-full after:size-5 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full
      "
    />

    <span class="ms-3 text-sm font-medium text-gray-500">
      Zmień skórkę
    </span>
  </label>
</template>
