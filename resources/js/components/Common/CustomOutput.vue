<script setup lang="ts">
defineProps<{
	label: string
	value: string
}>()
</script>

<template>
  <div class="w-full rounded-lg overflow-hidden bg-white/30 flex flex-col">
    <b class="bg-primary w-full text-white leading-6 py-2 px-4 text-sm duration-200 transition-colors">
      {{ label }}
    </b>

    <p class="py-3 px-4 border-2 border-t-0 h-full rounded-b-lg border-primary/30 font-medium duration-200 transition-colors">
      {{ value }}
    </p>
  </div>
</template>
