<script setup lang="ts">
import {useForm} from '@inertiajs/vue3'

const form = useForm({})

const props = defineProps<{
  isSuperAdmin: boolean
}>()

function logout() {
  form.get('/auth/logout')
}

function user() {
  form.get('/admin/users')
}

function admin() {
  form.get('/admin/admins')
}

</script>

<template>
  <div>
    Panel Administratora
  </div>
  <div>
    <form @submit.prevent="user">
      <button type="submit">User CRUD</button>
    </form>
    <form v-if="props.isSuperAdmin" @submit.prevent="admin">
      <button type="submit">Admin CRUD</button>
    </form>
    <form @submit.prevent="logout">
      <button type="submit">Logout</button>
    </form>
  </div>
</template>
