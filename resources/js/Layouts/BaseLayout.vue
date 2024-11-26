<script setup lang="ts">
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

function hideMessage() {
  status.value = undefined;
}

</script>

<template>
  <BackgroundEffect />

  <div class="flex flex-col items-center h-full min-h-screen">
    <Banner :show="!!status" :message="status" @close="hideMessage" />
    <Header :pages :user="props.user" :app-name="props.appName" />
    <slot />
    <Footer />
  </div>
</template>
