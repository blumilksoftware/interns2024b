<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import Header from '@/components/Common/Header.vue'
import Footer from '@/components/Common/Footer.vue'
import CustomInput from '@/components/Common/CustomInput.vue'
import BackgroundEffect2 from '@/components/Common/BackgroundEffect2.vue'


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
  form.post('/auth/password/reset')
}
</script>

<template>
  <Head>
    <title>Zmiana hasła</title>
    <meta name="Zmiana hasła" content="Zmiana hasła">
  </Head>
  <BackgroundEffect2 />
  <div class="flex flex-col items-center h-screen w-full">
    <Header title="Zmień hasło" />
    <div class="p-5 pt-12 w-full flex justify-center">
      <form class="p-6 gap-4 flex flex-col w-full bg-white/50 rounded-lg max-w-lg" @submit.prevent="submit">
        <CustomInput v-model="form.email" label="E-mail" :error="errors.email" name="email" type="email" />
        <CustomInput v-model="form.password" label="Nowe hasło" :error="errors.password" name="password" type="password" />
        <CustomInput v-model="form.password_confirmation" label="Powtórz hasło" :error="errors.password_confirmation" name="password_confirmation" type="password" />
        <button :disabled="form.processing" type="submit" class="bg-primary text-white font-bold py-3 px-4 rounded-lg disabled:bg-primary/70">
          Zmień hasło
        </button>
      </form>
    </div>
    <Footer />
  </div>
</template>
