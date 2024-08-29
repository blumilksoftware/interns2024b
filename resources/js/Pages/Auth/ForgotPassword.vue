<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import CustomInput from '@/components/Common/CustomInput.vue'
import Header from '@/components/Common/Header.vue'
import { ref } from 'vue'
import Banner from '@/components/Common/Banner.vue'
import Footer from '@/components/Home/Footer.vue'
import BackgroundEffect2 from '@/components/Common/BackgroundEffect2.vue'
import { Head } from '@inertiajs/vue3'


defineProps<{
  errors: Record<string, string>
  status?: string
}>()

const email = ref<string>()

function submit() {
  router.post('/auth/forgot-password', {email:email.value})
}
</script>

<template>
  <Head>
    <title>Profile</title>
    <meta name="Zmień hasło" content="User profile">
  </Head>
  <BackgroundEffect2 />
  <div class="flex flex-col items-center h-screen w-full">
    <Header title="Zmień hasło" />
    <Banner :content="status" />
    <div class="p-5 w-full">
      <form class="p-5 gap-5 flex flex-col w-full bg-white/70 rounded-lg " @submit.prevent="submit">
        <CustomInput v-model="email" label="E-mail" :error="errors.email" name="email" type="email" />
        <button type="submit" class="bg-primary text-white font-bold py-3 px-4 rounded-lg">
          Zmień hasło
        </button>
      </form>
    </div>
    <Footer />
  </div>
</template>
