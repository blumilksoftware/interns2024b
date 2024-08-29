<script setup lang="ts">
import type {Page} from "@/Types/Page";
import BaseLayout from "@/Layouts/BaseLayout.vue";

const pages: Page[] = [
  { title: 'Konkursy', href: '/dashboard' },
  { title: 'Mój Profil', href: '/profile' },
]
</script>

<template>
  <BaseLayout :pages>
    <slot />
  </BaseLayout>
</template>
