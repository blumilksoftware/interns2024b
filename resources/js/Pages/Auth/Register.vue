<script setup lang="ts">
import { reactive } from 'vue'
import { router } from '@inertiajs/vue3'

const { errors } = defineProps<{
  errors: Record<string, string[]>
}>()

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
    <div>
      <label for="name">Name:</label>
      <input v-model="form.name" name="name" type="text">
      <div v-if="errors.name">{{ errors.name }}</div>
    </div>
    <div>
      <label for="surname">Surname:</label>
      <input v-model="form.surname" name="surname" type="text">
      <div v-if="errors.surname">{{ errors.surname }}</div>
    </div>
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
    <div>
      <label for="school_id">School ID:</label>
      <input v-model="form.school_id" name="school_id" type="number">
      <div v-if="errors.school_id">{{ errors.school_id }}</div>
    </div>
    <button type="submit">Register</button>
  </form>
</template>
