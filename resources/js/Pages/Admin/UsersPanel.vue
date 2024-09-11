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
      <div>ID: {{ listedUser.id }}</div>
      <div>Imię: {{ listedUser.name }}</div>
      <div>Nazwisko: {{ listedUser.surname }}</div>
      <div>E-mail: {{ listedUser.email }}</div>
      <div>Szkoła: {{ listedUser.school.name }}</div>
      <div class="flex gap-4">
        <FormButton :disabled="listedUser.isAnonymized || !user?.isSuperAdmin" :href="`/admin/users/${listedUser.id}/anonymize`" method="patch" class="w-full" button-class="w-full">Anonimizuj użytkownika</FormButton>
        <LinkButton :href="`/admin/users/${listedUser.id}/edit`">Edit</LinkButton>
      </div>
    </div>
  </div>
</template>
