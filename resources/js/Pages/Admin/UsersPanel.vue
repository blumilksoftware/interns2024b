<script setup lang="ts">
import {type User} from '@/Types/User'
import FormButton from '@/components/Common/FormButton.vue'
import LinkButton from '@/components/Common/LinkButton.vue'
import {type PageProps} from '@/Types/PageProps'

defineProps<{
  users: User[]
}&PageProps>()
</script>

<template>
  <div class="flex flex-col items-start gap-4">
    <div v-for="listedUser in users" :key="listedUser.id" class="bg-white border shadow rounded-md p-4 w-full max-w-xl">
      <div><b>ID:</b> {{ listedUser.id }}</div>
      <div><b>Imię:</b> {{ listedUser.name }}</div>
      <div><b>Nazwisko:</b> {{ listedUser.surname }}</div>
      <div><b>E-mail:</b> {{ listedUser.email }}</div>
      <div><b>Szkoła:</b> {{ listedUser.school.name }}</div>
      <div class="flex gap-2 p-2">
        <FormButton small :disabled="listedUser.isAnonymized || !user?.isSuperAdmin" :href="`/admin/users/${listedUser.id}/anonymize`" method="patch" class="w-full" button-class="w-full">Anonimizuj użytkownika</FormButton>
        <LinkButton small :href="`/admin/users/${listedUser.id}/edit`">Edit</LinkButton>
      </div>
    </div>
  </div>
</template>
