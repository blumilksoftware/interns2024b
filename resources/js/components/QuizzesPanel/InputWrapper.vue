<script setup lang="ts">
const slots = defineSlots()
defineProps<{ label?:string, error?:string, hideError?:boolean }>()
</script>

<template>
  <div class="flex flex-1 flex-col">
    <label v-if="label" class="flex flex-1 flex-col 2xs:flex-row 2xs:gap-1">
      {{ label }}
      <slot v-if="!!slots.default" />
      <b v-else aria-label="brak danych">-</b>
    </label>
    <template v-else>
      <slot v-if="!!slots.default" />
      <b v-else aria-label="brak danych">-</b>
    </template>
    <span
      v-if="!hideError && error"
      :title="error"
      class="text-red text-sm truncate"
    >
      {{ error }}
    </span>
  </div>
</template>
