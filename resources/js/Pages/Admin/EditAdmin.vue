<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { defineProps } from 'vue'
import { type User } from '@/Types/User'
import { type School } from '@/Types/School'
import Button from "@/components/Common/Button.vue";

const props = defineProps<{
  user: User
  schools?: School[]
  errors: Record<string, string[]>
}>()

const form = useForm({
  name: props.user.name,
  surname: props.user.surname,
  email: props.user.email,
  school_id: props.user.school.id,
})

function update() {
  form.patch(`/admin/admins/${props.user.id}`)
}
</script>

<template>
  <div class="bg-white p-8 shadow border rounded-md">
    <h1 class="font-semibold text-xl text-primary">Edycja administratora</h1>

    <form @submit.prevent="update">
      <div class="mt-4">
        <label class="font-semibold text-nowrap block" for="name">Imię: </label>
        <input class="border w-full border-black p-2 bg-white rounded-md" id="name" v-model="form.name" type="text" required>
        <div v-if="errors.name" class="text-red">{{ errors.name }}</div>
      </div>

      <div class="mt-4">
        <label class="font-semibold text-nowrap block" for="name">Nazwisko: </label>
        <input class="border w-full border-black p-2 bg-white rounded-md" id="surname" v-model="form.surname" type="text" required>
        <div v-if="errors.surname" class="text-red">{{ errors.surname }}</div>
      </div>

      <div class="mt-4">
        <label class="font-semibold text-nowrap block" for="name">E-mail: </label>
        <input class="border w-full border-black p-2 bg-white rounded-md" id="email" v-model="form.email" type="email" required>
        <div v-if="errors.email" class="text-red">{{ errors.email }}</div>
      </div>

      <div class="mt-4">
        <label class="font-semibold text-nowrap block" for="name">Hasło: </label>
        <input class="border w-full border-black p-2 bg-white rounded-md" id="password" v-model="form.password" type="password" required>
        <div v-if="errors.password" class="text-red">{{ errors.password }}</div>
      </div>

      <Button class="mt-4 w-full" type="submit">Zmień</Button>
    </form>
  </div>
</template>
