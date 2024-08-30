<script lang="ts" setup>

import { ref, provide } from 'vue'
import Footer from '@/components/Common/Footer.vue'
import AuthBanner from '@/components/Home/AuthBanner.vue'
import GeneralSection from '@/components/Home/GeneralSection.vue'
import AuthSection from '@/components/Home/AuthSection.vue'
import BackgroundEffect from '@/components/Home/BackgroundEffect.vue'
import {type School} from '@/Types/School'

const isLogin = ref(null)

const { errors, schools } = defineProps<{
  errors: Record<string, string[]>
  schools: School[]
}>()

provide('isLoginRef', isLogin)
</script>

<template>
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
