<script setup lang="ts">
import { reactive } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps<
{
  token: string
  errors: Record<string, string[]>
}>()

const form = reactive({
  email: null,
  password: null,
  password_confirmation: null,
  token: props.token,
})

function submit() {
  router.post('/auth/password/reset', form)
}
</script>

<template>
  <form @submit.prevent="submit">
    <div>
      <label for="email">Email:</label>
      <input v-model="form.email" name="email" type="email"><br>
      <div v-if="errors.email">{{ errors.email }}</div>
    </div>
    <div>
      <label for="email">Password:</label>
      <input v-model="form.password" name="password" type="password" required><br>
      <label for="email">Password Confirmation:</label>
      <input v-model="form.password_confirmation" name="password_confirmation" type="password"><br>
      <div v-if="errors.password">{{ errors.password }}</div>
    </div>

    <div>
      <input v-model="form.token" name="token" type="hidden">
      <div v-if="errors.token">{{ errors.token }}</div>
    </div>
    <button type="submit">Change Password?</button>
  </form>
</template>
