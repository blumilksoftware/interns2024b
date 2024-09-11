<script setup lang="ts">
import {Head} from '@inertiajs/vue3'
import LinkButton from "@/components/Common/LinkButton.vue";
import FormButton from "@/components/Common/FormButton.vue";
import {School} from "@/Types/School";

defineProps<{ schools: School[] }>()

</script>

<template>
  <div class="flex flex-col items-start gap-4">
    <div v-for="school in schools" :key="school.id" class="bg-white border shadow rounded-md p-4 w-full max-w-xl">
      <div>ID: {{ school.id }}</div>
      <div>Nazwa: {{ school.name }}</div>
      <div>Miasto: {{ school.city }}</div>

      <div class="flex gap-2 p-2">
        <LinkButton small :href="`/admin/schools/${school.id}/edit`">Edit</LinkButton>
        <FormButton small :href="`/admin/schools/${school.id}`" method="delete">Delete</FormButton>
      </div>
    </div>

    <FormButton href="/admin/schools/create" method="get" class="w-full" button-class="w-full">Dodaj nową szkołę</FormButton>

  </div>
</template>
