<script setup lang="ts">
import BaseLayout from '@/Layouts/BaseLayout.vue'
import type {PageProps} from '@/Types/PageProps'

const props = defineProps<PageProps>()

const pages: Page[] = []

if (!!props.user?.isAdmin || props.user?.isSuperAdmin) {
  pages.push(
    { title: 'Testy', href: '/admin/quizzes' },
    { title: 'Szkoły', href: '/admin/schools' },
    { title: 'Uczniowie', href: '/admin/users' },
  )
}
else {
  pages.push({ title: 'Konkursy', href: '/dashboard' })
}

pages.push(
  { title: 'Profil', href: '/profile' },
)

if (props.user?.isSuperAdmin) {
  pages.push({ title: 'Administratorzy', href: '/admin/admins'})
}
</script>

<template>
  <BaseLayout :pages :app-name="appName" :user :flash>
    <slot />
  </BaseLayout>
</template>
