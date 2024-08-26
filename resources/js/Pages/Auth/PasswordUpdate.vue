<script setup lang="ts">

import {useForm} from '@inertiajs/vue3'

const props = defineProps<
  {
    errors: Record<string, string[]>
  }>()

const form = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
})

function updatePassword() {
  form.patch('/profile/password', {
    preserveScroll: true,
    onSuccess: () => {
      form.reset()
    },
  })
}

</script>

<template>
  <div>
    <form @submit.prevent="updatePassword">
      <label for="current_password">Aktualne hasło:</label>
      <input v-model="form.current_password" name="password" type="password" required><br>
      <div v-if="errors.current_password">{{ props.errors.current_password }}</div>
      <label for="password">Nowe hasło:</label>
      <input v-model="form.password" name="password" type="password"><br>
      <div v-if="errors.password">{{ props.errors.password }}</div>
      <label for="password_confirmation">Potwierdzenie nowego hasła:</label>
      <input v-model="form.password_confirmation" name="password_confirmation" type="password"><br>
      <div v-if="errors.password_confirmation">{{ props.errors.password_confirmation }}</div>
      <button type="submit">Zmień hasło</button>
    </form>
  </div>
</template>
