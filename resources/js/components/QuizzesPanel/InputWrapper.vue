<script setup lang="ts">
import { ref } from 'vue'

defineProps<{
  label?: string
  error?: string
  hideError?: boolean
  hideContent?: boolean
  column?: boolean
  row?: boolean
  wrapperClass?: string
}>()

const wrapper = ref<HTMLElement>()
</script>

<template>
  <div class="flex flex-1 flex-col">
    <div
      ref="wrapper"
      class="flex flex-1"
      :class="[
        wrapperClass,
        {
          'flex-col': column,
          'flex-row': row,
          'flex-col 2xs:flex-row 2xs:gap-1': column === row,
        }
      ]"
    >
      <label
        v-if="label"
        class="size-fit"
        @click="wrapper?.querySelector('input')?.focus()"
      >
        {{ label }}
      </label>
    
      <b
        v-if="hideContent"
        aria-label="brak danych"
      >
        -
      </b>

      <slot v-else />
    </div>

    <span
      v-if="!hideError && error"
      :title="error"
      class="text-red text-sm"
    >
      {{ error }}
    </span>
  </div>
</template>
