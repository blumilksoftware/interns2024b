<script setup lang="ts">
import CustomInput from '@/components/Common/CustomInput.vue'
import { useForm } from '@inertiajs/vue3'
import { type Errors } from '@inertiajs/core'

const { errors } = defineProps<{ errors:Errors }>()
const form = useForm({
  email: '',
  password: '',
})

function submit() {
  form.post('/auth/login', { preserveScroll: true, preserveState: true })
}
</script>

<template>
  <form class="row-start-1 col-start-1 space-y-6" @submit.prevent="submit">
    <CustomInput v-model="form.email" label="E-mail" :error="errors.email" name="email" type="email" />

    <div class="flex flex-col gap-2">
      <CustomInput v-model="form.password" label="Hasło" :error="errors.password" name="password" type="password" />
      <a href="/auth/forgot-password" class="duration-200 font-semibold leading-6 text-primary hover:text-primary-dark text-sm">Nie pamiętam hasła</a>
    </div>

    <div>
      <button
        :disabled="form.processing"
        type="submit"
        class="rounded-lg text-md flex w-full justify-center bg-primary p-3 font-bold text-white
        transition hover:bg-primary-dark focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary
        disabled:opacity-70"
      >
        Zaloguj się
      </button>
    </div>
  </form>
</template>
