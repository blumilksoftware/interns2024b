<script setup lang="ts">
import type { PageProps } from '@/Types/PageProps'
import BaseLayout from '@/Layouts/BaseLayout.vue'

defineProps<PageProps>()
</script>

<template>
  <BaseLayout
    :pages="[]"
    :app-name="appName"
    :user
    :flash
  >
    <slot />
  </BaseLayout>
</template>
