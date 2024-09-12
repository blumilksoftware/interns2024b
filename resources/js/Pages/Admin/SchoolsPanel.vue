<script setup lang="ts">
import LinkButton from '@/components/Common/LinkButton.vue'
import FormButton from '@/components/Common/FormButton.vue'
import {type SchoolFull} from '@/Types/SchoolFull'
import {ref, watch} from 'vue'
import axios from 'axios'
import {router} from '@inertiajs/vue3'

defineProps<{ schools: SchoolFull[] }>()

const status = ref(false)
setInterval(() => {
  axios.get('/admin/schools/status')
    .then(res => status.value = res.data.done)
    .catch(() => status.value = false)
}, 1000)

watch(status, () => router.reload())

</script>

<template>
  <div class="flex flex-col items-start gap-4">
    <div v-for="school in schools" :key="school.id" class="bg-white border shadow rounded-md p-4 w-full max-w-xl">
      <div><b>ID: </b> {{ school.id }}</div>
      <div><b>Nazwa: </b> {{ school.name }}</div>
      <div><b>Miejscowość: </b> {{ school.city }}</div>
      <div><b>Ulica: </b> {{ school.street }}</div>
      <div><b>Numer budynku: </b> {{ school.building }}</div>
      <div><b>Numer lokalu: </b> {{ school.apartment ?? '' }}</div>
      <div><b>Kod pocztowy: </b> {{ school.zipCode }}</div>
      <div><b>Uczniowie: </b> {{ school.users }}</div>

      <div class="flex gap-2 p-2">
        <FormButton small :disabled="school.users > 0" :href="`/admin/schools/${school.id}`" method="delete" class="w-full" button-class="w-full">Usuń</FormButton>
        <LinkButton small :href="`/admin/schools/${school.id}/edit`">Edit</LinkButton>
      </div>
    </div>

    <FormButton href="/admin/schools/create" method="get" class="w-full" button-class="w-full">Dodaj nową szkołę</FormButton>
    <FormButton :disabled="!status" href="/admin/schools/fetch" method="post" class="w-full" button-class="w-full">Pobierz szkoły</FormButton>
  </div>
</template>
