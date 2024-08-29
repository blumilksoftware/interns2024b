<script setup lang="ts">
import {useForm} from '@inertiajs/vue3'

const form = useForm({})

function logout() {
  form.get('/auth/logout')
}

function submit() {
  form.get('/profile')
}
</script>

<template>
  <div>
    User Dashboard
  </div>
  <div>
    <form @submit.prevent="submit">
      <button type="submit">Profile</button>
    </form>
    <form @submit.prevent="logout">
      <button type="submit">Logout</button>
    </form>
  </div>
</template>
