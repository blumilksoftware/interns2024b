<script setup lang="ts">
import { type User } from '@/Types/User'
defineProps<{ user: User }>()
</script>

<template>
  <div class="flex flex-col gap-4 justify-content">
    <b class="text-[1.2rem] sm:text-center lg:text-left">Twoje dane</b>
    <div class="flex flex-col sm:grid sm:grid-cols-2 gap-4">
      <div class="w-full rounded-lg overflow-hidden bg-white/30 flex flex-col">
        <b class="bg-primary w-full text-white p-2 px-3">Imię</b>
        <p class="p-4 text-[1.1rem] border-2 border-t-0 h-full rounded-b-lg border-primary/30">{{ user.name }}</p>
      </div>
      <div class="w-full rounded-lg overflow-hidden bg-white/30 flex flex-col">
        <b class="bg-primary w-full text-white p-2 px-3">Nazwisko</b>
        <p class="p-4 text-[1.1rem] border-2 border-t-0 h-full rounded-b-lg border-primary/30">{{ user.surname }}</p>
      </div>
      <div class="w-full rounded-lg overflow-hidden bg-white/30 flex flex-col">
        <b class="bg-primary w-full text-white p-2 px-3">E-mail</b>
        <p class="p-4 text-[1.1rem] border-2 border-t-0 h-full rounded-b-lg border-primary/30">{{ user.email }}</p>
      </div>
      <div class="w-full rounded-lg overflow-hidden bg-white/30 flex flex-col">
        <b class="bg-primary w-full text-white p-2 px-3">Szkoła</b>
        <p class="p-4 text-[1.1rem] border-2 border-t-0 h-full rounded-b-lg border-primary/30">{{ user.school.name }}</p>
      </div>
    </div>
  </div>
</template>
