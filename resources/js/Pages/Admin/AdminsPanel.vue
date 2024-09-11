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
      <div><b>ID:</b> {{ user.id }}</div>
      <div><b>Imię:</b> {{ user.name }}</div>
      <div><b>Nazwisko:</b> {{ user.surname }}</div>
      <div><b>E-mail:</b> {{ user.email }}</div>
      <div><b>Szkoła:</b> {{ user.school.name }}</div>

      <div class="flex gap-2 p-2">
        <FormButton class="w-full" button-class="w-full" small :href="`/admin/admins/${user.id}`" method="delete">Delete</FormButton>
        <LinkButton small :href="`/admin/admins/${user.id}/edit`">Edit</LinkButton>
      </div>
    </div>

    <FormButton href="/admin/admins/create" method="get" class="w-full" button-class="w-full">Dodaj nowego administratora</FormButton>

  </div>
</template>
