<script setup lang="ts">

import {useForm} from '@inertiajs/vue3'

const form = useForm({})

function logout() {
  form.post('/auth/logout')
}

function sent() {


  form.post('/email/verification-notification')
}

</script>

<template>
  <form @submit.prevent="sent">
    <button type="submit">Wyślij ponownie link weryfikacyjny</button>
  </form>

  <form @submit.prevent="logout">
    <button type="submit">Logout</button>
  </form>
</template>

