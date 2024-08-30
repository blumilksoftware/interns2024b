<script setup lang="ts">

import {type User} from '@/Types/User'
import {useForm} from '@inertiajs/vue3'

const form = useForm({})
const props = defineProps<{
  user: User
}>()

function edit() {
  form.get(`/admin/admins/${props.user.id}`)
}

function destroy() {
  form.delete(`/admin/admins/${props.user.id}`)
}
</script>

<template>
  <div class="ml-8 border-2">
    <div>ID: {{ props.user.id }}</div>
    <div>Imię: {{ props.user.name }}</div>
    <div>Nazwisko: {{ props.user.surname }}</div>
    <div>E-mail: {{ props.user.email }}</div>
    <div>Szkoła: {{ props.user.school.name }}</div>
    <form @submit.prevent="edit">
      <button type="submit">Edit</button>
    </form>
    <form @submit.prevent="destroy">
      <button :disabled="form.processing" type="submit">Delete</button>
    </form>
  </div>
</template>

