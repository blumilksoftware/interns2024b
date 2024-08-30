<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import CustomInput from '@/components/Common/CustomInput.vue'
import Header from '@/components/Common/Header.vue'
import Banner from '@/components/Common/Banner.vue'
import Footer from '@/components/Common/Footer.vue'
import BackgroundEffect2 from '@/components/Common/BackgroundEffect2.vue'
import { Head } from '@inertiajs/vue3'
import { ref } from 'vue'


defineProps<{
  errors: Record<string, string>
  status?: string
}>()

const form = useForm({ email: '' })

const isVisible = ref<boolean>(false)

function submit() {
  form.post('/auth/forgot-password', {
    onSuccess: () => {
      isVisible.value = true
    },
  })
}
</script>

<template>
  <Head>
    <title>Przypomnij hasło</title>
    <meta name="Przypomnij hasło" content="Przypomnij hasło">
  </Head>
  <BackgroundEffect2 />
  <div class="flex flex-col items-center h-screen w-full">
    <Header title="Zmień hasło" />
    <Transition>
      <Banner v-if="status && isVisible" :text="status" @click="isVisible = false" />
    </Transition>
    <div class="sm:p-6 sm:pt-12 size-full flex justify-center sm:h-fit">
      <form class="p-6 gap-4 flex flex-col size-full bg-white/50 sm:rounded-lg sm:max-w-lg sm:h-fit" @submit.prevent="submit">
        <CustomInput v-model="form.email" label="E-mail" :error="form.errors.email" name="email" type="email" />
        <button :disabled="form.processing" type="submit" class="bg-primary text-white font-bold py-3 px-4 rounded-lg disabled:bg-primary/70">
          Przypomnij hasło
        </button>
      </form>
    </div>
    <Footer />
  </div>
</template>
