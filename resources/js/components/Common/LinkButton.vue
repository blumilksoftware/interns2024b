<script setup lang="ts">
defineProps<{
    small?: boolean
    href: string
  }>()
</script>

<template>
  <a
    :href="href"
    class="bg-primary px-3 semibold text-white  font-semibold shadow-sm hover:bg-primary-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600"
    :class="{
      'rounded-md text-sm py-2': small,
      'rounded-lg py-3': !small
    }"
  >
    <slot />
  </a>
</template>


