<script setup lang="ts">
defineProps<{
	label: string
	value: string
}>()
</script>

<template>
  <div class="flex flex-col gap-3 p-5 w-full border rounded-xl overflow-hidden bg-white/70 shadow">
    <b class="text-primary transition-colors">
      {{ label }}
    </b>
    
    <p class="text-gray-800">
      {{ value }}
    </p>
  </div>
</template>
