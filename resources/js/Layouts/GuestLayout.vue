<script setup lang="ts">

import BackgroundEffect from '@/components/Common/BackgroundEffect.vue'
import Banner from '@/components/Common/Banner.vue'
import Footer from '@/components/Common/Footer.vue'
import Header from '@/components/Common/Header.vue'
import type {PageProps} from '@/Types/PageProps'
import { provide, ref, watch } from 'vue'

const props = defineProps<PageProps>()
const status = ref<string | undefined>(props.flash.status)
const header = ref()
provide('header', header)

watch(
  () => props.flash,
  flash => {
    status.value = flash.status
  }, { immediate: true },
)
</script>

<template>
  <BackgroundEffect />

  <div class="flex flex-col items-center h-full min-h-screen">
    <Banner v-model="status" />
    <Header ref="header" :pages="[]" :user="props.user" :app-name="props.appName" />
    <slot />
    <Footer />
  </div>
</template>
