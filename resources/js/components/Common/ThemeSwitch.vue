<script setup lang="ts">
function toggleTheme() {
  const isThemeTauron = document.documentElement.classList.contains('theme-tauron')
  document.documentElement.classList.remove(isThemeTauron ? 'theme-tauron' : 'theme-witelon')
  document.documentElement.classList.add(isThemeTauron ? 'theme-witelon' : 'theme-tauron')
}
</script>

<template>
  <label class="inline-flex items-center cursor-pointer">
    <input type="checkbox" class="sr-only peer" @click="toggleTheme">
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
