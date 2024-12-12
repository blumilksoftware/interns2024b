<script setup lang="ts">
import { type Errors } from '@inertiajs/core'
import PasswordUpdate from '@/components/Profile/PasswordUpdateSection.vue'
import UserData from '@/components/Profile/UserDataSection.vue'
import { Head } from '@inertiajs/vue3'
import { type PageProps } from '@/Types/PageProps'

defineProps<{
  user: User
  errors: Errors
} & Omit<PageProps, 'user' | 'errors'>>()
</script>

<template>
  <Head title="Profil" />
  
  <div class="flex flex-col justify-center gap-x-[8vw] gap-y-12 px-[5vw] w-full lg:flex-row py-4 lg:py-16">
    <UserData :user="user" />

    <PasswordUpdate :errors="errors" />
  </div>
</template>
