<script lang="ts" setup>
import { ref, provide } from 'vue'
import Footer from '@/components/Home/Footer.vue'
import RegisterBanner from '@/components/Home/RegisterBanner.vue'
import GeneralSection from '@/components/Home/GeneralSection.vue'
import AuthSection from '@/components/Home/AuthSection.vue'
import BackgroundEffect from '@/components/Home/BackgroundEffect.vue'

const isLogin = ref(null)
provide('isLoginRef', isLogin)
</script>

<template>
  <BackgroundEffect />
  <RegisterBanner :is-login="isLogin" />
  <div class="flex flex-col lg:flex-row lg:justify-evenly lg:gap-x-[5vw] lg:px-[5vw]">
    <GeneralSection />
    <AuthSection ref="isLogin" />
  </div>
  <Footer />
</template>
