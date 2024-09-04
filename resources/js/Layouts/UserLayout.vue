<script setup lang="ts">

import type {Page} from '@/Types/Page'
import BaseLayout from '@/Layouts/BaseLayout.vue'
import type {PageProps} from '@/Types/PageProps'
import { route } from "ziggy-js";

defineProps<PageProps>()

const pages: Page[] = [
  { title: 'Konkursy', href: route('dashboard') },
  { title: 'Profil', href: route('profile') },
]
</script>

<template>
  <BaseLayout :pages :app-name="appName" :user :flash>
    <slot />
  </BaseLayout>
</template>
