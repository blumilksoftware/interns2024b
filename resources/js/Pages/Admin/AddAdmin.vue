<script setup lang="ts">

import { useForm } from '@inertiajs/vue3'
import { defineProps } from 'vue'

const props = defineProps<{
  errors: Record<string, string[]>
}>()

const form = useForm({
  name: null,
  surname: null,
  email: null,
  school_id: 1,
  password: null,
})

function submit() {
  form.post('/admin/admins/add')
}
</script>

<template>
  Dodawanie nowego administratora

  <form @submit.prevent="submit">
    <div>
      <label for="name">Imię</label>
      <div>
        <input id="name" v-model="form.name" required name="name" type="text"
               :class="{'ring-red focus:ring-red':props.errors.name}"
        >
        <div v-if="props.errors.name" class="text-red">{{ props.errors.name }}</div>
      </div>
    </div>

    <div>
      <label for="surname">Nazwisko</label>
      <div>
        <input id="surname" v-model="form.surname" required name="surname" type="text"
               :class="{'ring-red focus:ring-red':props.errors.surname}"
        >
        <div v-if="props.errors.surname" class="text-red">{{ props.errors.surname }}</div>
      </div>
    </div>

    <div>
      <label for="email">E-mail</label>
      <div>
        <input id="email" v-model="form.email" required name="email" type="email"
               :class="{'ring-red focus:ring-red':props.errors.email}"
        >
        <div v-if="props.errors.email" class="text-red">{{ props.errors.email }}</div>
      </div>
    </div>

    <div>
      <label for="password">Hasło</label>
      <div>
        <input id="password" v-model="form.password" required name="password" type="password" autocomplete="current-password"
               :class="{'ring-red focus:ring-red':props.errors.password}"
        >
        <div v-if="props.errors.email" class="text-red">{{ props.errors.email }}</div>
      </div>
    </div>
    <button type="submit">Dodaj</button>
  </form>
</template>
