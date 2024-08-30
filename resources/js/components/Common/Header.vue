<script setup lang="ts">
defineProps<{title:string}>()
</script>

<template>
  <header class="w-full font-bold p-5 bg-white/50">
    {{ title }}
  </header>
</template>
