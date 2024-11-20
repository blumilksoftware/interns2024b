<script setup lang="ts">
import CustomRadio from '@/components/Common/CustomRadio.vue'

defineProps<{
  checked?: boolean
  correct?: boolean
  hasRanking?: boolean
}>()
</script>

<template>
  <label 
    class="flex gap-2.5" 
    :class="{
      'text-red': !correct && checked && hasRanking,
      'text-green-700': correct && hasRanking,
      'text-primary': !correct && !checked || !hasRanking,
      'font-semibold': checked,
    }"
  >
    <CustomRadio
      class="mt-1"
      disabled
      :checked="checked"
    />
    <slot />
  </label>
</template>
