<script setup lang="ts">
import {defineProps} from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps<{
  user: {
    name: string
    surname: string
    email: string
    }
  errors: Record<string, string[]>
  status?: string
  }>()


function submit() {
  router.post('/profile/forgot-password')
}
</script>

<template>
  <div>
    <p>Imię: {{ props.user.name }}</p>
    <p>Nazwisko: {{ props.user.surname }}</p>
    <p>Email: {{ props.user.email }}</p>
    <form @submit.prevent="submit">
      <button type="submit">Zmiana hasła</button>
    </form>
    <div v-if="status" class="alert alert-success">{{ status }}</div>
  </div>
</template>

<style scoped>

</style>
