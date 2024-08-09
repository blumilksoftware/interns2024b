<script setup>
import { reactive } from 'vue'
import { router } from '@inertiajs/vue3'

const form = reactive({
  name: null,
  surname: null,
  email: null,
  password: null,
  school_id: null,
})

function submit() {
  router.post('/auth/register', form)
}
</script>

<template>
  <form @submit.prevent="submit">
    <label for="name">Name:</label>
    <input v-model="form.name" name="name" type="text">
    <label for="surname">Surname:</label>
    <input v-model="form.surname" name="surname" type="text">
    <label for="email">Email:</label>
    <input v-model="form.email" name="email" type="email">
    <label for="password">password:</label>
    <input v-model="form.password" name="password" type="password">
    <label for="school_id">School ID:</label>
    <input v-model="form.school_id" name="school_id" type="number">
    <button type="submit">Register</button>
  </form>
</template>
