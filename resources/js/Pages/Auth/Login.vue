<script setup lang="ts">
import { reactive } from 'vue'
import { router } from '@inertiajs/vue3'

const { errors } = defineProps<{
  errors: Record<string, string[]>
}>()

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
    <div>
      <label for="email">Email:</label>
      <input v-model="form.email" name="email" type="email">
      <div v-if="errors.email">{{ errors.email }}</div>
    </div>
    <div>
      <label for="password">password:</label>
      <input v-model="form.password" name="password" type="password">
      <div v-if="errors.password">{{ errors.password }}</div>
    </div>
    <button type="submit">Login</button>
  </form>

  <a href="/auth/forgot-password">I forgot the password :(</a>
</template>
