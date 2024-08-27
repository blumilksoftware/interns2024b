<script setup lang="ts">

import {useForm} from '@inertiajs/vue3'

const form = useForm({})

function logout() {
  form.get('/auth/logout')
}

function submit() {
  form.get('/admin/users')
}


</script>

<template>
  <div>
    Panel Administratora
  </div>
  <div>
    <form @submit.prevent="submit">
      <button type="submit">User CRUD</button>
    </form>
    <form @submit.prevent="logout">
      <button type="submit">Logout</button>
    </form>
  </div>
</template>
