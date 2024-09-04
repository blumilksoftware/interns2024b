<script setup lang="ts">

import {useForm} from '@inertiajs/vue3'
import { route } from "ziggy-js";

const form = useForm({})

function logout() {
  form.post( route('logout'))
}

function sent() {
  form.post(route('verification.send'))
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

