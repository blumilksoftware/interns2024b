<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { type School } from '@/Types/School'
import { type User } from '@/Types/User'

const props = defineProps<{
  user: User
  schools?: School[]
}>()

const form = useForm({
  name: props.user.name,
  surname: props.user.surname,
  email: props.user.email,
  school_id: props.user.school.id,
})

function update() {
  form.patch(`/admin/users/${props.user.id}`)
}
</script>

<template>
  <div>
    <h1>Edycja Użytkownika</h1>

    <form @submit.prevent="update">
      <div>ID: {{ props.user.id }}</div>

      <div>
        <label for="name">
          Imię
        </label>

        <input
          id="name"
          v-model="form.name"
          type="text"
        >
      </div>

      <div>
        <label for="surname">
          Nazwisko
        </label>

        <input
          id="surname"
          v-model="form.surname"
          type="text"
        >
      </div>

      <div>
        <label for="email">
          E-mail
        </label>

        <input
          id="email"
          v-model="form.email"
          type="email"
        >
      </div>

      <div>
        <label for="school">
          Szkoła
        </label>

        <select
          id="school"
          v-model="form.school_id"
        >
          <option
            v-for="school in schools"
            :key="school.id"
            :value="school.id"
          >
            {{ school.name }}
          </option>
        </select>
      </div>

      <button type="submit">
        Update
      </button>
    </form>
  </div>
</template>
