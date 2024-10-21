<script lang="ts" setup>
import {ref, provide, watch} from 'vue'
import Footer from '@/components/Common/Footer.vue'
import AuthBanner from '@/components/Home/AuthBanner.vue'
import GeneralSection from '@/components/Home/GeneralSection.vue'
import AuthSection from '@/components/Home/AuthSection.vue'
import BackgroundEffect from '@/components/Common/BackgroundEffect.vue'
import {type School} from '@/Types/School'
import {type PageProps} from '@/Types/PageProps'
import Banner from '@/components/Common/Banner.vue'

const isLogin = ref(null)

const { errors, schools, ...props } = defineProps<{
  errors: Record<string, string[]>
  schools: School[]
} & PageProps>()

const status = ref<string | undefined>(props.flash.status)

watch(() => props.flash, flash => {
  status.value = flash.status
}, { immediate: true })


provide('isLoginRef', isLogin)
</script>

<template>
  <Banner v-model="status" />

  <div class="flex flex-col h-screen">
    <BackgroundEffect />
    <AuthBanner :is-login="isLogin" />
    <div class="flex flex-col lg:flex-row lg:justify-evenly lg:gap-x-[5vw] lg:px-[5vw]">
      <GeneralSection />
      <AuthSection ref="isLogin" :errors="errors" :schools="schools" />
    </div>
    <Footer />
  </div>
</template>

<style scoped>
html {
  overflow-y: scroll;
}
</style>
