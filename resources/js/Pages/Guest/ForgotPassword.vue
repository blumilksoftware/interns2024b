<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import CustomInput from '@/components/Common/CustomInput.vue'
import { Head } from '@inertiajs/vue3'

defineProps<{
  errors: Record<string, string>
  status?: string
}>()

const form = useForm({ email: '' })

function submit() {
  form.post('/auth/forgot-password')
}
</script>

<template>
  <Head>
    <title>Przypomnij hasło</title>
    <meta name="Przypomnij hasło" content="Przypomnij hasło">
  </Head>

  <div class="sm:p-6 sm:pt-12 size-full flex justify-center sm:h-fit">
    <form class="p-6 gap-4 flex flex-col size-full bg-white shadow border sm:rounded-lg sm:max-w-lg sm:h-fit" @submit.prevent="submit">
      <CustomInput v-model="form.email" label="E-mail" :error="form.errors.email" name="email" type="email" />
      <button :disabled="form.processing" type="submit" class="bg-primary text-white font-bold py-3 px-4 rounded-lg disabled:bg-primary/70">
        Przypomnij hasło
      </button>
    </form>
  </div>
</template>
