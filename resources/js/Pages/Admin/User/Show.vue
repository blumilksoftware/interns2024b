<script setup lang="ts">

import {type User} from '@/Types/User'
import {useForm} from '@inertiajs/vue3'

const form = useForm({})

const props = defineProps<{
  user: User
  isSuperAdmin: boolean
}>()

function edit() {
  form.get(`/admin/users/${props.user.id}`)
}

function anonymize() {
  form.patch(`/admin/users/anonymize/${props.user.id}`)
}
</script>

<template>
  <div class="ml-8 border-2">
    <div>ID: {{ props.user.id }}</div>
    <div>Imię: {{ props.user.name }}</div>
    <div>Nazwisko: {{ props.user.surname }}</div>
    <div>E-mail: {{ props.user.email }}</div>
    <div>Szkoła: {{ props.user.school.name }}</div>
    <div v-if="!props.user.is_anonymized">
      <form @submit.prevent="edit">
        <button type="submit">Edit</button>
      </form>
      <form v-if="props.isSuperAdmin" @submit.prevent="anonymize">
        <button :disabled="form.processing" type="submit">Anonymize</button>
      </form>
    </div>
  </div>
</template>

