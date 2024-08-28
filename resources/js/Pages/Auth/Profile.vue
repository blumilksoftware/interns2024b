<script setup lang="ts">
import { defineProps } from 'vue'
import BackgroundEffect from '@/components/Home/BackgroundEffect.vue'
import Footer from '@/components/Common/Footer.vue'
import Header from '@/components/Common/Header.vue'
import { useForm } from '@inertiajs/vue3'

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

const form = useForm({})

function submit() {
  form.get('/profile/password-edit')
}
</script>

<template>
  <BackgroundEffect />
  <div class="inset-0 bg-white/30 absolute -z-10" />
  <div class="flex flex-col items-center h-screen">
    <Header title="Profil" />
    <div class="flex flex-col gap-4 p-5 w-full">
      <div class="flex flex-col w-full sm:grid sm:grid-cols-2 gap-4">
        <div class="w-full border border-primary/30 py-2 px-4 rounded-lg bg-white/30">
          <b>Imię:</b> 
          <p>{{ props.user.name }}</p>
        </div>
        <div class="w-full border border-primary/30 py-2 px-4 rounded-lg bg-white/30">
          <b>Nazwisko:</b> 
          <p>{{ props.user.surname }}</p>
        </div>
        <div class="w-full border border-primary/30 py-2 px-4 rounded-lg bg-white/30">
          <b>E-mail:</b> 
          <p>{{ props.user.email }}</p>
        </div>
        <div class="w-full border border-primary/30 py-2 px-4 rounded-lg bg-white/30">
          <b>Szkoła:</b> 
          <p>{{ props.user.school.name }}</p>
        </div>
      </div>
      <hr class="border-primary/30 mx-5">
      <form @submit.prevent="submit">
        <button class="bg-primary text-white font-bold py-3 px-4 rounded-lg w-full" type="submit">Zmień hasło</button>
      </form>
      <div v-if="status" class="alert alert-success">{{ status }}</div>
    </div>
    <Footer />
  </div>
</template>
