<script setup lang="ts">
import BaseLayout from '@/Layouts/BaseLayout.vue'
import type {PageProps} from '@/Types/PageProps'

defineProps<PageProps>()

const pages: Page[] = [
  { title: 'Konkursy', href: '/dashboard' },
  { title: 'Profil', href: '/profile' },
]
</script>

<template>
  <BaseLayout :pages :app-name="appName" :user :flash>
    <slot />
  </BaseLayout>
</template>
