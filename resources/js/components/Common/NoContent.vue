<script setup lang="ts">
withDefaults(defineProps<{ title?: string, description?: string }>(), {
  title: 'Brak Danych',
  description: 'Wygląda na to, że nic tu jeszcze nie ma. Kliknij przycisk poniżej, aby dodać tu trochę treści.',
})
</script>

<template>
  <div class="flex flex-col gap-1 p-5 bg-white/70 border-2 rounded-xl shadow-sm">
    <h1 class="text-xl text-primary font-bold">{{ title }}</h1>
    <p class="mb-1 text-gray-500">{{ description }}</p>
    <slot />
  </div>
</template>
