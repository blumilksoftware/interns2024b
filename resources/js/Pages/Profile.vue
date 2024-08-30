<script setup lang="ts">

import { defineProps } from 'vue'
import BackgroundEffect from '@/components/Home/BackgroundEffect.vue'
import Footer from '@/components/Common/Footer.vue'
import Header from '@/components/Common/Header.vue'
import PasswordUpdate from '@/components/Profile/PasswordUpdateSection.vue'
import UserData from '@/components/Profile/UserDataSection.vue'
import { type User } from '@/Types/User'
import { Head } from '@inertiajs/vue3'

defineProps<{
  user: User
  errors: Record<string, string>
  }>()

</script>

<template>
  <Head>
    <title>Profile</title>
    <meta name="Profil" content="Profil użytkownika">
  </Head>
  <BackgroundEffect />
  <div class="inset-0 bg-white/60 fixed -z-10" />
  <div class="flex flex-col items-center h-screen">
    <Header title="Profil" />
    <div class="flex flex-col justify-center gap-x-[5vw] gap-y-12 px-[5vw] w-full lg:flex-row py-4 sm:py-16">
      <UserData :user="user" />
      <PasswordUpdate :errors="errors" />
    </div>
    <Footer />
  </div>
</template>
