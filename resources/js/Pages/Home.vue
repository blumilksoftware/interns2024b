<script lang="ts" setup>

import {ref, watch} from 'vue'
import Footer from '@/components/Common/Footer.vue'
import AuthBanner from '@/components/Home/AuthBanner.vue'
import GeneralSection from '@/components/Home/GeneralSection.vue'
import AuthSection from '@/components/Home/AuthSection.vue'
import BackgroundEffect from '@/components/Common/BackgroundEffect.vue'
import {type School} from '@/Types/School'
import {type PageProps} from '@/Types/PageProps'
import Banner from '@/components/Common/Banner.vue'
import { Head } from '@inertiajs/vue3'

const authSectiontRef = ref<InstanceType<typeof AuthSection>>()

const { errors, schools, ...props } = defineProps<{
  errors: Record<string, string[]>
  schools: School[]
} & PageProps>()

const status = ref<string | undefined>(props.flash.status)

function scrollToAuth(isLogin:boolean) {
  if (!authSectiontRef.value) return 
  authSectiontRef.value.isLogin = isLogin
  if (!authSectiontRef.value.authSectionElement) return
  const element = authSectiontRef.value.authSectionElement

  const offsetTop = element.getBoundingClientRect().top + window.scrollY
  window.scrollTo({
    top: offsetTop,
    behavior: 'smooth',
  })
}

watch(
  () => props.flash,
  flash => {
    status.value = flash.status
  },
  { immediate: true },
)

</script>

<template>
  <Head>
    <title>Strona główna</title>
  </Head>
  <Transition>
    <Banner v-if="status" :text="status" @click="status = ''" />
  </Transition>

  <div class="flex flex-col h-screen">
    <BackgroundEffect />
    <AuthBanner :is-login="authSectiontRef?.isLogin" @scroll-to-auth="scrollToAuth" />
    <div class="flex flex-col lg:flex-row lg:justify-evenly lg:gap-x-[5vw] lg:px-[5vw]">
      <GeneralSection />
      <AuthSection ref="authSectiontRef" :errors="errors" :schools="schools" />
    </div>
    <Footer />
  </div>
</template>

<style>
html {
  overflow-y: scroll;
}
</style>
