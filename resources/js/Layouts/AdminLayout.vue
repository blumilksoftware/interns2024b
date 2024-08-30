<script setup lang="ts">

import type {Page} from '@/Types/Page'
import BaseLayout from '@/Layouts/BaseLayout.vue'

const pages: Page[] = [
  { title: 'Testy', href: '/admin/quizzes' },
  { title: 'Szkoły', href: '/admin/schools' },
  { title: 'Uczniowie', href: '/admin/users' },
  { title: 'Ranking', href: '/admin/ranking' },
]
</script>

<template>
  <BaseLayout :pages>
    <slot />
  </BaseLayout>
</template>
