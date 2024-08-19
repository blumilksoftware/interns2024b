<script setup lang="ts">
import { reactive } from 'vue'
import { router } from '@inertiajs/vue3'

const { errors } = defineProps<{
  errors: Record<string, string[]>
}>()

const form = reactive({
  email: null,

})

function submit() {
  router.post('/auth/forgot-password', form)
}
</script>

<template>
  <form @submit.prevent="submit">
    <div>
      <label for="email">Email:</label>
      <input v-model="form.email" name="email" type="email">
      <div v-if="errors.email">{{ errors.email }}</div>
    </div>
    <button type="submit">Reset?</button>
  </form>
</template>
