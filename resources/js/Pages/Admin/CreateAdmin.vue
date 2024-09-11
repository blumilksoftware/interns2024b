<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { defineProps } from 'vue'
import Button from '@/components/Common/Button.vue'

defineProps<{
  errors: Record<string, string[]>
}>()

const form = useForm({
  name: null,
  surname: null,
  email: null,
  school_id: 1,
  password: null,
})

function submit() {
  form.post('/admin/admins')
}
</script>

<template>
  <div class="bg-white p-8 shadow border rounded-md">
    <h1 class="font-semibold text-xl text-primary">Dodawanie nowego administratora</h1>

    <form @submit.prevent="submit">
      <div class="mt-4">
        <label class="font-semibold text-nowrap block" for="name">Imię: </label>
        <input id="name" v-model="form.name" class="border w-full border-black p-2 bg-white rounded-md" type="text" required>
        <div v-if="errors.name" class="text-red">{{ errors.name }}</div>
      </div>

      <div class="mt-4">
        <label class="font-semibold text-nowrap block" for="name">Nazwisko: </label>
        <input id="surname" v-model="form.surname" class="border w-full border-black p-2 bg-white rounded-md" type="text" required>
        <div v-if="errors.surname" class="text-red">{{ errors.surname }}</div>
      </div>

      <div class="mt-4">
        <label class="font-semibold text-nowrap block" for="name">E-mail: </label>
        <input id="email" v-model="form.email" class="border w-full border-black p-2 bg-white rounded-md" type="email" required>
        <div v-if="errors.email" class="text-red">{{ errors.email }}</div>
      </div>

      <div class="mt-4">
        <label class="font-semibold text-nowrap block" for="name">Hasło: </label>
        <input id="password" v-model="form.password" class="border w-full border-black p-2 bg-white rounded-md" type="password" required>
        <div v-if="errors.password" class="text-red">{{ errors.password }}</div>
      </div>

      <Button class="mt-4 w-full" type="submit">Dodaj</Button>
    </form>
  </div>
</template>
