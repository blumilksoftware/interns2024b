<script setup lang="ts">

import CustomInput from '@/components/Common/CustomInput.vue'
import {useForm} from '@inertiajs/vue3'

defineProps< { errors: Record<string, string> }>()

const form = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
})

function updatePassword() {
  form.patch('/profile/password', {
    preserveScroll: true,
    onSuccess: () => {
      form.reset()
    },
  })
}

</script>

<template>
  <form class="flex flex-col gap-4" @submit.prevent="updatePassword">
    <CustomInput v-model="form.current_password" label="Aktualne hasło" type="password" name="password" :error="errors.current_password" />
    <CustomInput v-model="form.password" label="Nowe hasło" type="password" name="password" :error="errors.password" />
    <CustomInput v-model="form.password_confirmation" label="Potwierdź nowe hasło" type="password" name="password" :error="errors.password_confirmation" />
    <button type="submit" class="bg-primary text-white font-bold py-4 rounded-lg">Zmień hasło</button>
  </form>
</template>
