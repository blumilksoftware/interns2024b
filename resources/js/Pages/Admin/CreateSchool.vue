<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { defineProps } from 'vue'
import Button from '@/components/Common/Button.vue'

defineProps<{
  errors: Record<string, string[]>
}>()

const form = useForm({
  name: null,
  city: null,
  street: null,
  building_number: null,
  apartment_number: null,
  zip_code: null,
})

function save() {
  form.post('/admin/schools')
}
</script>

<template>
  <div class="bg-white p-8 shadow border rounded-md">
    <h1 class="font-semibold text-xl text-primary">Tworzenie Szkoły</h1>
    <form @submit.prevent="save">
      <div class="mt-4">
        <label class="font-semibold text-nowrap block" for="name">Nazwa: </label>
        <input id="name" v-model="form.name" class="w-full border border-black p-2 bg-white rounded-md" type="text">
        <div v-if="errors.name" class="text-red">{{ errors.name }}</div>
      </div>

      <div class="mt-4">
        <label class="font-semibold text-nowrap block" for="city">Miejscowość</label>
        <input id="city" v-model="form.city" class="w-full border border-black p-2 bg-white rounded-md" type="text">
        <div v-if="errors.city" class="text-red">{{ errors.city }}</div>
      </div>

      <div class="mt-4">
        <label class="font-semibold text-nowrap block" for="street">Ulica</label>
        <input id="street" v-model="form.street" class="w-full border border-black p-2 bg-white rounded-md" type="text">
        <div v-if="errors.street" class="text-red">{{ errors.street }}</div>
      </div>

      <div class="mt-4">
        <label class="font-semibold text-nowrap block" for="building_number">Numer budynku</label>
        <input id="building_number" v-model="form.building_number" class="w-full border border-black p-2 bg-white rounded-md" type="text">
        <div v-if="errors.building_number" class="text-red">{{ errors.building_number }}</div>
      </div>

      <div class="mt-4">
        <label class="font-semibold text-nowrap block" for="apartment_number">Numer lokalu</label>
        <input id="apartment_number" v-model="form.apartment_number" class="w-full border border-black p-2 bg-white rounded-md" type="text">
        <div v-if="errors.apartment_number" class="text-red">{{ errors.apartment_number }}</div>
      </div>

      <div class="mt-4">
        <label class="font-semibold text-nowrap block" for="zip_code">Kod pocztowy</label>
        <input id="zip_code" v-model="form.zip_code" class="w-full border border-black p-2 bg-white rounded-md" type="text">
        <div v-if="errors.zip_code" class="text-red">{{ errors.zip_code }}</div>
      </div>

      <Button class="mt-4 w-full" type="submit">Zapisz</Button>
    </form>
  </div>
</template>
