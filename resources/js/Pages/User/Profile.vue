<script setup lang="ts">
import { type Errors } from '@inertiajs/core'
import PasswordUpdate from '@/components/Profile/PasswordUpdateSection.vue'
import UserData from '@/components/Profile/UserDataSection.vue'
import { Head } from '@inertiajs/vue3'

defineProps<{
  user: User
  errors: Errors
  }>()
</script>

<template>
  <Head>
    <title>Profil</title>
    <meta name="Profil" content="Profil użytkownika">
  </Head>

  <div class="flex flex-col justify-center gap-x-[5vw] gap-y-12 px-[5vw] w-full lg:flex-row py-4 sm:py-16">
    <UserData :user="user" />
    <PasswordUpdate :errors="errors" />
  </div>
</template>
