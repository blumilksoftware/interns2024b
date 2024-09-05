<script setup lang="ts">

import type {Page} from '@/Types/Page'
import BaseLayout from '@/Layouts/BaseLayout.vue'
import {type PageProps} from '@/Types/PageProps'

const props = defineProps<PageProps>()

const pages: Page[] = [
  { title: 'Testy', href: '/admin/quizzes' },
  { title: 'Szkoły', href: '/admin/schools' },
  { title: 'Uczniowie', href: '/admin/users' },
]

if (props.user?.isSuperAdmin) {
  pages.push({ title: 'Administratorzy', href: '/admin/admins'})
}
</script>

<template>
  <BaseLayout :pages :app-name="appName" name :user :flash>
    <slot />
  </BaseLayout>
</template>
