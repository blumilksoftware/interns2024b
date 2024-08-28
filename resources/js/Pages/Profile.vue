<script setup lang="ts">
import { defineProps } from 'vue'
import BackgroundEffect from '@/components/Home/BackgroundEffect.vue'
import Footer from '@/components/Common/Footer.vue'
import Header from '@/components/Common/Header.vue'
import PasswordUpdate from '@/components/Profile/PasswordUpdateSection.vue'
import UserData from '@/components/Profile/UserDataSection.vue'

defineProps<{
  user: {
    name: string
    surname: string
    email: string
    school: {
      name: string
      }
    }
  errors: Record<string, string[]>
  }>()

</script>

<template>
  <BackgroundEffect />
  <div class="inset-0 bg-white/60 fixed -z-10" />
  <div class="flex flex-col items-center h-screen">
    <Header title="Profil" />
    <div class="flex flex-col gap-4 p-5 w-full sm:w-fit">
      <div class="flex flex-col gap-4">
        <b class="text[1.1rem]">Twoje dane</b>
        <UserData :user="user" />
      </div>
      <hr class="border-primary/30 my-5">
      <div class="flex flex-col gap-4">
        <b class="text[1.1rem]">Zmień hasło</b>
        <PasswordUpdate :errors="errors" />
      </div>
    </div>
    <Footer />
  </div>
</template>


