<script setup lang="ts">

import type {Page} from '@/Types/Page'
import Header from '@/components/Common/Header.vue'
import Footer from '@/components/Common/Footer.vue'
import BackgroundEffect from '@/components/Home/BackgroundEffect.vue'

defineProps<{ pages: Page[] }>()
</script>

<template>
  <BackgroundEffect />
  <div class="inset-0 bg-white/60 fixed -z-10" />
  <div class="flex flex-col items-center h-full min-h-screen">
    <Header :pages />
    <slot />
    <Footer />
  </div>
</template>
