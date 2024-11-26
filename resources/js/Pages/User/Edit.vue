<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import {type PageProps} from '@/Types/PageProps'

const props = defineProps<{
  user: User
  schools?: School[]
} & Omit<PageProps, 'user'>>()

const form = useForm({
  firstname: props.user.firstname,
  surname: props.user.surname,
  email: props.user.email,
  school_id: props.user.school.id,
})

function update() {
  form.patch(`/admin/users/${props.user.id}`)}
</script>

<template>
  <div>
    <h1>Edycja Użytkownika</h1>
    <form @submit.prevent="update">
      <div>ID: {{ props.user.id }}</div>
      <div>
        <label for="firstname">Imię</label>
        <input id="firstname" v-model="form.firstname" type="text">
      </div>

      <div>
        <label for="surname">Nazwisko</label>
        <input id="surname" v-model="form.surname" type="text">
      </div>

      <div>
        <label for="email">E-mail</label>
        <input id="email" v-model="form.email" type="email">
      </div>

      <div>
        <label for="school">Szkoła</label>
        <select id="school" v-model="form.school_id">
          <option v-for="school in schools" :key="school.id" :value="school.id">
            {{ school.name }}
          </option>
        </select>
      </div>

      <button type="submit">Update</button>
    </form>
  </div>
</template>
