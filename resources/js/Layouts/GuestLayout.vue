<script setup lang="ts">
import BackgroundEffect from '@/components/Common/BackgroundEffect.vue'
import Banner from '@/components/Common/Banner.vue'
import Footer from '@/components/Common/Footer.vue'
import Header from '@/components/Common/Header.vue'
import type {PageProps} from '@/Types/PageProps'
import { provide, ref, watch } from 'vue'

const props = defineProps<PageProps>()
const status = ref<string | undefined>(props.flash.status)
const titleRef = ref('')
provide('titleRef', titleRef)

watch(() => props.flash, flash => {
  status.value = flash.status
}, { immediate: true })

function hideMessage() {
  status.value = undefined
}
</script>

<template>
  <BackgroundEffect />

  <div class="flex flex-col items-center h-full min-h-screen">
    <Banner :show="!!status" :message="status" @close="hideMessage" />
    <Header :title="titleRef" :pages="[]" :user="props.user" :app-name="props.appName" />

    <slot />

    <Footer />
  </div>
</template>
