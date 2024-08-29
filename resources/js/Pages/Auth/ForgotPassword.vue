<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import CustomInput from '@/components/Common/CustomInput.vue'
import { ref } from 'vue'
import Banner from '@/components/Common/Banner.vue'
import BackgroundEffect from '@/components/Home/BackgroundEffect.vue'

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
  <BackgroundEffect />
  <Banner :content="status" />
  <form class="mt-8 p-5 gap-5 flex flex-col" @submit.prevent="submit">
    <CustomInput v-model="email" label="E-mail" :error="errors.email" name="email" type="email" />
    <button type="submit" class="bg-primary text-white font-bold py-3 px-4 rounded-lg">
      Zmień hasło
    </button>
  </form>
</template>
