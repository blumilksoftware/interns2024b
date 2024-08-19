<script setup lang="ts">
import { reactive } from 'vue'
import { router } from '@inertiajs/vue3'

const { errors, status } = defineProps<{
  errors: Record<string, string[]>
  status?: string
}>()

console.log(status)

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
      <button type="submit">Reset?</button>
      <div v-if="errors.email">{{ errors.email }}</div>
    </div>
    <div v-if="status" class="alert alert-success">{{ status }}</div>
  </form>
</template>
