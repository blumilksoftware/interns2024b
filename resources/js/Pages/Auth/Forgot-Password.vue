<script setup>
import { reactive } from 'vue'
import { router } from '@inertiajs/vue3'

const form = reactive({
  email: null,

})

function submit() {
  router.post('/auth/forget-password', form)
}
</script>

<template>
  <form @submit.prevent="submit">
    <label for="email">Email:</label>
    <input v-model="form.email" name="email" type="email">
    <button type="submit">Reset?</button>
  </form>
</template>
