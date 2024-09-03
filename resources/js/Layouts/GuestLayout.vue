<script setup lang="ts">

import BaseLayout from '@/Layouts/BaseLayout.vue'
import type {PageProps} from '@/Types/PageProps'

defineProps<PageProps>()
</script>

<template>
  <BaseLayout :pages="[]" :appName :user :flash>
    <slot />
  </BaseLayout>
</template>
