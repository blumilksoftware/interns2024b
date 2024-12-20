<script setup lang="ts">
import CustomRadio from '@/components/Common/CustomRadio.vue'

defineProps<{
  checked?: boolean
  correct?: boolean
  revealAnswers?: boolean
}>()
</script>

<template>
  <label 
    class="flex gap-2.5" 
    :class="{
      'text-red': !correct && checked && revealAnswers,
      'text-green-700': correct && revealAnswers,
      'font-semibold text-primary': checked,
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
