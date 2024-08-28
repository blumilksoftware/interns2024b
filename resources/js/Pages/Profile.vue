<script setup lang="ts">
import { defineProps } from 'vue'
import BackgroundEffect from '@/components/Home/BackgroundEffect.vue'
import Footer from '@/components/Common/Footer.vue'
import Header from '@/components/Common/Header.vue'
import PasswordUpdate from '@/Pages/Auth/PasswordUpdate.vue'
import UserData from '@/Pages/Auth/UserData.vue'

const props = defineProps<{
  user: {
    name: string
    surname: string
    email: string
    school: {
      name: string
      }
    }
  errors: Record<string, string[]>
  status?: string
  }>()

</script>

<template>
  <BackgroundEffect />
  <div class="inset-0 bg-white/30 absolute -z-10" />
  <div class="flex flex-col items-center h-screen">
    <Header title="Profil" />
    <div class="flex flex-col gap-4 p-5 w-full sm:w-fit">
      <b class="text[1.1rem]">Twoje dane:</b>
      <UserData {{...props}} />
      <hr class="border-primary/30 m-5">
      <PasswordUpdate :errors="errors" />
      <div v-if="status" class="alert alert-success">{{ status }}</div>
    </div>
    <Footer />
  </div>
</template>


