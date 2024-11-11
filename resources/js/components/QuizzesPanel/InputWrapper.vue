<script setup lang="ts">
defineProps<{
  label?:string,
  error?:string,
  hideError?:boolean,
  hideContent?:boolean
}>()
</script>

<template>
  <div class="flex flex-1 flex-col">
    <label v-if="label" class="flex flex-1 flex-col 2xs:flex-row 2xs:gap-1">
      {{ label }}
      <b v-if="hideContent" aria-label="brak danych">-</b>
      <slot v-else />
    </label>
    <template v-else>
      <b v-if="hideContent" aria-label="brak danych">-</b>
      <slot v-else />
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
