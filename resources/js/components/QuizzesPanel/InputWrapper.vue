<script setup lang="ts">
defineProps<{ label?: string, error?: string, hideError?: boolean, hideContent?: boolean, column?: boolean, row?: boolean }>()
</script>

<template>
  <div class="flex flex-1 flex-col">
    <label
      class="flex flex-1"
      :class="{
        'flex-col': column,
        'flex-row': row,
        'flex-col 2xs:flex-row 2xs:gap-1': column === row,
      }"
    >
      {{ label }}
      <b
        v-if="hideContent"
        aria-label="brak danych"
      >
        -
      </b>

      <slot v-else />
    </label>

    <span
      v-if="!hideError && error"
      :title="error"
      class="text-red text-sm truncate"
    >
      {{ error }}
    </span>
  </div>
</template>
