<script setup lang="ts">
defineProps<{
    error?: string
  }>()
</script>

<template>
  <div v-if="error" class="font-semibold text-sm text-red text-left">
    {{ error }}
  </div>
</template>
