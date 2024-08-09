<script setup>
import { reactive } from 'vue'
import { router } from '@inertiajs/vue3'

const form = reactive({
  name: null,
  surname: null,
  email: null,
  password: null,
  school_id: null,
})

function submit() {
  router.post('/auth/register', form)
}
</script>

<template>
  <a href="/auth/login">Login</a><br>
  <a href="/auth/register">Register</a>
</template>
