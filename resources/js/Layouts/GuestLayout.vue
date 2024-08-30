<script setup lang="ts">

import BaseLayout from '@/Layouts/BaseLayout.vue'
</script>

<template>
  <BaseLayout :pages="[]">
    <slot />
  </BaseLayout>
</template>
