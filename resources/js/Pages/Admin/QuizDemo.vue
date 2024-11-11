<script setup lang="ts">
defineProps<{quiz: Quiz}>()
</script>

<template>
  Demo page {{ quiz.id }}
</template>
