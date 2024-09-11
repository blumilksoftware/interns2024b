<script setup lang="ts">
import {type User} from '@/Types/User'
import FormButton from '@/components/Common/FormButton.vue'
import LinkButton from '@/components/Common/LinkButton.vue'

defineProps<{
  users: User[]
}>()
</script>

<template>
  <div class="flex flex-col items-start gap-4">
    <div v-for="user in users" :key="user.id" class="bg-white border shadow rounded-md p-4 w-full max-w-xl">
      <div>ID: {{ user.id }}</div>
      <div>Imię: {{ user.name }}</div>
      <div>Nazwisko: {{ user.surname }}</div>
      <div>E-mail: {{ user.email }}</div>
      <div>Szkoła: {{ user.school.name }}</div>

      <div class="flex gap-4">
        <LinkButton :href="`/admin/admins/${user.id}/edit`">Edit</LinkButton>
        <FormButton :href="`/admin/admins/${user.id}`" method="delete">Delete</FormButton>
      </div>
    </div>

    <FormButton href="/admin/admins/create" method="get" class="w-full" button-class="w-full">Dodaj nowego administratora</FormButton>

  </div>
</template>
