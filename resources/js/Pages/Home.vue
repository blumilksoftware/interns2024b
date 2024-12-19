<script lang="ts" setup>
import { ref, watch } from 'vue'
import { Head } from '@inertiajs/vue3'
import { type Errors } from '@inertiajs/core'
import Footer from '@/components/Common/Footer.vue'
import AuthBanner from '@/components/Home/AuthBanner.vue'
import Banner from '@/components/Common/Banner.vue'
import GeneralSection from '@/components/Home/GeneralSection.vue'
import AuthSection from '@/components/Home/AuthSection.vue'
import BackgroundEffect from '@/components/Common/BackgroundEffect.vue'
import { type PageProps } from '@/Types/PageProps'

const authSectionRef = ref<InstanceType<typeof AuthSection>>()
const props = defineProps<{ errors: Errors } & PageProps>()
const status = ref<string | undefined>(props.flash.status)

watch(
  () => props.flash, 
  flash => {
    status.value = flash.status
  }, 
  { immediate: true },
)

function hideMessage() {
  status.value = undefined
}

function scrollToAuth(isLogin: boolean) {
  if (!authSectionRef.value?.authSectionElement) return
  authSectionRef.value.isLogin = isLogin
  const element = authSectionRef.value.authSectionElement
  const offsetTop = element.getBoundingClientRect().top + window.scrollY
  window.scrollTo({
    top: offsetTop,
    behavior: 'smooth',
  })
}
</script>

<template>
  <Head title="Strona główna" />

  <Banner
    :show="!!status"
    :message="status"
    @close="hideMessage"
  />

  <div class="flex flex-col h-screen">
    <BackgroundEffect />

    <AuthBanner
      :is-login="authSectionRef?.isLogin"
      @scroll-to-auth="scrollToAuth"
    />

    <div class="flex flex-col lg:flex-row lg:justify-evenly lg:gap-x-[5vw] lg:px-[5vw]">
      <GeneralSection />

      <AuthSection
        ref="authSectionRef"
        :errors="errors"
      />
    </div>

    <Footer />
  </div>
</template>

<style>
html {
  overflow-y: scroll;
}
</style>
