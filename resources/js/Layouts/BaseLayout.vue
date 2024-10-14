<script setup lang="ts">
import type {Page} from '@/Types/Page'
import Header from '@/components/Common/Header.vue'
import Footer from '@/components/Common/Footer.vue'
import BackgroundEffect from '@/components/Common/BackgroundEffect.vue'
import Banner from '@/components/Common/Banner.vue'
import {ref, watch} from 'vue'
import {type PageProps} from '@/Types/PageProps'

const props = defineProps<{ pages: Page[] } & PageProps>()
const status = ref<string | undefined>(props.flash.status)

watch(() => props.flash, flash => {
  status.value = flash.status
}, { immediate: true })

</script>

<template>
  <BackgroundEffect />

  <div class="flex flex-col items-center h-full min-h-screen">
    <Banner v-model="status" />
    <Header :pages :user="props.user" :app-name="props.appName" />
    <slot />
    <Footer />
  </div>
</template>
