<script setup lang="ts">
import { defineProps } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps<{
  user: {
    name: string
    surname: string
    email: string
    school: {
      name: string
      }
    }
  errors: Record<string, string[]>
  }>()

const form = useForm({})

function submit() {
  form.get('/profile/password-edit')
}
</script>

<template>
  <div>
    <p>Imię: {{ props.user.name }}</p>
    <p>Nazwisko: {{ props.user.surname }}</p>
    <p>E-mail: {{ props.user.email }}</p>
    <p>Szkoła: {{ props.user.school.name }}</p>
    <form @submit.prevent="submit">
      <button type="submit">Formularz zmiany hasła</button>
    </form>
  </div>
</template>
