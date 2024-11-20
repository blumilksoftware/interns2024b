<script setup lang="ts">
import CustomRadio from '@/components/Common/CustomRadio.vue'

defineProps<{
  checked?: boolean
  mode?: 'success' | 'error'
  bold?: boolean
}>()
</script>

<template>
  <label 
    class="flex gap-2.5 text-primary" 
    :class="{
      'text-red': mode==='error' && checked,
      'text-green-700': mode==='success' && checked,
      'font-semibold': checked || bold
    }"
  >
    <CustomRadio
      class="mt-1"
      disabled
      :checked="checked || bold"
    />
    <slot />
  </label>
</template>
