<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import CustomInput from '@/components/Common/CustomInput.vue'
import { Head } from '@inertiajs/vue3'
import { inject, onMounted, type Ref } from 'vue'
import type Header from '@/components/Common/Header.vue'

const headerRef = inject<Ref<InstanceType<typeof Header>>>('header')
onMounted(() => {
  if (headerRef)
    headerRef.value.titleRef = 'Przypomnij hasło'
})
const form = useForm({ email: '' })

function submit() {
  form.post('/auth/forgot-password')
}
</script>

<template>
  <Head>
    <title>{{ headerRef?.titleRef }}</title>
    <meta name="Przypomnij hasło" content="Przypomnij hasło">
  </Head>

  <div class="sm:p-6 sm:pt-12 size-full flex flex-col items-center justify-center sm:h-fit gap-6">
    <div class="text-md font-medium leading-6 text-gray-900 px-[5vw] text-center flex flex-col gap-1">
      <p>Wpisz adres e-mail przypisany do Twojego konta.</p>
      <p>Na ten adres wyślemy instrukcje dotyczące zresetowania hasła.</p>
    </div>
    <form class="p-6 gap-6 flex flex-col size-full sm:max-w-lg sm:h-fit" @submit.prevent="submit">
      <CustomInput v-model="form.email" label="E-mail" :error="form.errors.email" name="email" type="email" />
      <button :disabled="form.processing" type="submit" class="bg-primary text-white font-bold py-3 px-4 rounded-lg disabled:bg-primary/70">
        Przypomnij hasło
      </button>
    </form>
  </div>
</template>
