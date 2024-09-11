<script setup lang="ts">
import {type User} from '@/Types/User'
import FormButton from '@/components/Common/FormButton.vue'
import LinkButton from '@/components/Common/LinkButton.vue'

const props = defineProps<{
  users: User[]
}>()
</script>

<template>
  <h1 v-if="props.users.length > 0" class="text-xl">ADMINISTRATORZY</h1>

  <div>
    <FormButton href="/admin/admins/create" method="post" />
    <form @submit.prevent="addAdmin">
      <button type="submit">Dodaj nowego administratora</button>
    </form>
  </div>

  <div v-for="item in props.users" :key="item.id" class="ml-8 border-2">
    <div>ID: {{ props.user.id }}</div>
    <div>Imię: {{ props.user.name }}</div>
    <div>Nazwisko: {{ props.user.surname }}</div>
    <div>E-mail: {{ props.user.email }}</div>
    <div>Szkoła: {{ props.user.school.name }}</div>

    <LinkButton :href="`/admin/admins/${item.id}/edit`">Edit</LinkButton>
    <FormButton :href="`/admin/admins/${item.id}`" method="delete">Edit</FormButton>
  </div>
</template>
