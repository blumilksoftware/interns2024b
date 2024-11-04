<script setup lang="ts">
import type { Page } from '@/Types/Page'
import type { PageProps } from '@/Types/PageProps'
import BaseLayout from '@/Layouts/BaseLayout.vue'

defineProps<PageProps>()

const pages: Page[] = [
  { title: 'Konkursy', href: '/dashboard' },
  { title: 'Profil', href: '/profile' },
]
</script>

<template>
  <BaseLayout
    :pages
    :app-name="appName"
    :user
    :flash
  >
    <slot />
  </BaseLayout>
</template>
