<script setup>
import { reactive } from 'vue'
import { router } from '@inertiajs/vue3'

const form = reactive({
  email: null,
  password: null,
})

function submit() {
  router.post('/auth/login', form)
}
</script>

<template>
  <form @submit.prevent="submit">
    <label for="email">Email:</label>
    <input v-model="form.email" name="email" type="email">
    <label for="password">password:</label>
    <input v-model="form.password" name="password" type="password">
    <button type="submit">Login</button>
  </form>
</template>
