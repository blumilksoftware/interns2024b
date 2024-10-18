<script setup lang="ts">
defineProps<{ label?:string, hasContent:boolean, error?:string, showError:boolean }>()
</script>

<template>
  <div class="flex flex-col">
    <label v-if="label" class="flex flex-col 2xs:flex-row 2xs:gap-1">
      {{ label }}
      <slot v-if="hasContent" />
      <b v-else aria-label="brak danych">-</b>
    </label>
    <template v-else>
      <slot v-if="hasContent" />
      <b v-else aria-label="brak danych">-</b>
    </template>
    <span
      v-if="showError && error"
      :title="error"
      class="text-red text-sm truncate"
    >
      {{ error }}
    </span>
  </div>
</template>
