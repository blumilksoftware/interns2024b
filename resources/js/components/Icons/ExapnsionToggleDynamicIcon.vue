<script setup lang="ts">
import { ChevronRightIcon } from '@heroicons/vue/24/outline'

defineProps<{ expanded:boolean }>()
</script>

<template>
  <ChevronRightIcon class="transition-transform" :class="{ 'rotate-90' : expanded }" />
</template>
