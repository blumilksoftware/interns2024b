<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { defineProps } from 'vue'
import { type User } from '@/Types/User'
import { type School } from '@/Types/School'
import Button from '@/components/Common/Button.vue'

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
  form.patch(`/admin/users/${props.user.id}`)
}
</script>

<template>
  <div class="bg-white p-8 shadow border rounded-md">
    <h1 class="font-semibold text-xl text-primary">Edycja Użytkownika</h1>
    <form @submit.prevent="update">
      <div>ID: {{ props.user.id }}</div>
      <div class="mt-4">
        <label class="font-semibold text-nowrap block" for="name">Imię</label>
        <input id="name" v-model="form.name" class="border border-black p-2 bg-white rounded-md  w-full" type="text">
        <div v-if="errors.name" class="text-red">{{ errors.name }}</div>
      </div>

      <div class="mt-4">
        <label class="font-semibold text-nowrap block" for="surname">Nazwisko</label>
        <input id="surname" v-model="form.surname" class="border border-black p-2 bg-white rounded-md  w-full" type="text">
        <div v-if="errors.surname" class="text-red">{{ errors.surname }}</div>
      </div>

      <div class="mt-4">
        <label class="font-semibold text-nowrap block" for="email">E-mail</label>
        <input id="email" v-model="form.email" class="border border-black p-2 bg-white rounded-md w-full" type="email">
        <div v-if="errors.email" class="text-red">{{ errors.email }}</div>
      </div>

      <div class="mt-4">
        <label class="font-semibold text-nowrap block" for="school">Szkoła</label>
        <select id="school" v-model="form.school_id" class="border border-black p-2 bg-white rounded-md w-full">
          <option v-for="school in schools" :key="school.id" :value="school.id" class="bg-white">
            {{ school.name }}
          </option>
        </select>
        <div v-if="errors.school_id" class="text-red">{{ errors.school_id }}</div>
      </div>

      <Button class="mt-4 w-full" type="submit">Zmień</Button>
    </form>
  </div>
</template>
