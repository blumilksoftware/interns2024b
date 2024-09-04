<script setup lang="ts">

import type {Page} from '@/Types/Page'
import BaseLayout from '@/Layouts/BaseLayout.vue'
import {type PageProps} from '@/Types/PageProps'
import { route } from 'ziggy-js'

const props = defineProps<PageProps>()

const pages: Page[] = [
  { title: 'Testy', href: route('admin.quizzes.index') },
  { title: 'Szkoły', href: route('admin.schools.index') },
  { title: 'Uczniowie', href: route('admin.users.index') },
]

if (props.user?.isSuperAdmin) {
  pages.push({ title: 'Administratorzy', href: route('admin.admins.index')})
}
</script>

<template>
  <BaseLayout :pages :app-name="appName" :user :flash>
    <slot />
  </BaseLayout>
</template>
