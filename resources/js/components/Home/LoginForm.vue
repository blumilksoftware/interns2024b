<script setup lang="ts">
import { ref } from 'vue'
import { Request } from '@/scripts/request'
import CustomInput from '../Common/CustomInput.vue'

const { errors } = defineProps<{
  errors: Record<string, string>
}>()

const form = ref({
  email: '',
  password: '',
})

const request = new Request()

function submit() {
  request.sendRequest('/auth/login', {method: 'post', data: form.value, preserveScroll: true, preserveState: true})
}
</script>

<template>
  <form class="row-start-1 col-start-1 space-y-6 min-h-512" @submit.prevent="submit">
    <CustomInput v-model="form.email" label="E-mail" :error="errors.email" name="email" type="email" />
    
    <div class="flex flex-col gap-2">
      <CustomInput v-model="form.password" label="Hasło" :error="errors.password" name="password" type="password" />
      <a href="/auth/forgot-password" class="duration-200 font-semibold leading-6 text-primary hover:text-primary-950 text-sm">Nie pamiętam hasła</a>
    </div>

    <div>
      <button 
        :disabled="request.isRequestOngoing.value"
        type="submit"
        class="rounded-lg text-md flex w-full justify-center bg-primary p-3 font-bold text-white
        transition hover:bg-primary-950 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary
        disabled:opacity-70"
      >
        Zaloguj się
      </button>
    </div>
  </form>
</template>
