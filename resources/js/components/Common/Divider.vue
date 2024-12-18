<script setup lang="ts">
const slots = defineSlots()
</script>

<template>
  <div class="w-full flex flex-col 2xs:flex-row 2xs:items-center px-5 xs:px-0">
    <div class="xs:mx-4 w-0 xs:w-7 border-t border-primary duration-200 transition-colors" />

    <slot />

    <div
      v-if="slots.default"
      class="xs:mx-4 flex-1 w-full border-t border-transparent xs:border-primary duration-200 transition-colors"
    />

    <slot name="center" />

    <div
      v-if="slots.center"
      class="xs:mx-4 flex-1 w-full border-t border-transparent xs:border-primary duration-200 transition-colors"
    />

    <slot name="right" />

    <div
      v-if="slots.right"
      class="xs:mx-4 w-0 xs:w-7 border-t border-primary duration-200 transition-colors"
    />
  </div>
</template>
