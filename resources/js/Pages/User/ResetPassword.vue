<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import CustomInput from '@/components/Common/CustomInput.vue'
import { route } from 'ziggy-js'

const props = defineProps<{
  token: string
  errors: Record<string, string>
}>()

const form = useForm({
  email: '',
  password: '',
  password_confirmation: '',
  token: props.token,
})

function submit() {
  form.post(route('password.update'))
}
</script>

<template>
  <Head>
    <title>Zmiana hasła</title>
    <meta name="Zmiana hasła" content="Zmiana hasła">
  </Head>

  <div class="sm:p-6 sm:pt-12 size-full flex justify-center sm:h-fit">
    <form class="p-6 gap-4 flex flex-col size-full bg-white/50 sm:rounded-lg sm:max-w-lg sm:h-fit" @submit.prevent="submit">
      <CustomInput v-model="form.email" label="E-mail" :error="errors.email" name="email" type="email" />
      <CustomInput v-model="form.password" label="Nowe hasło" :error="errors.password" name="password" type="password" />
      <CustomInput v-model="form.password_confirmation" label="Powtórz hasło" :error="errors.password_confirmation" name="password_confirmation" type="password" />
      <button :disabled="form.processing" type="submit" class="bg-primary text-white font-bold py-3 px-4 rounded-lg disabled:bg-primary/70">
        Zmień hasło
      </button>
    </form>
  </div>
</template>
