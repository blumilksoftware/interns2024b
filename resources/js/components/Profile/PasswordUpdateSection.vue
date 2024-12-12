<script setup lang="ts">
import CustomInput from '@/components/Common/CustomInput.vue'
import { useForm } from '@inertiajs/vue3'
import Divider from '@/components/Common/Divider.vue'

defineProps< { errors: Record<string, string> }>()

const form = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
})

function updatePassword() {
  form.patch(
    '/profile/password', 
    {
      preserveScroll: true,
      onSuccess: () => {
        form.reset()
      },
    },
  )
}
</script>

<template>
  <div class="flex flex-col gap-4">
    <Divider>
      <b class="text-lg text-primary duration-200 transition-colors">
        Zmień hasło
      </b>
    </Divider>

    <div class="flex lg:justify-center sm:min-w-96 justify-center">
      <form
        class="flex flex-col gap-4 w-full sm:max-w-96"
        @submit.prevent="updatePassword"
      >
        <CustomInput
          v-model="form.current_password"
          label="Aktualne hasło"
          type="password"
          name="password"
          :error="errors.current_password"
        />

        <CustomInput
          v-model="form.password"
          label="Nowe hasło"
          type="password"
          name="password"
          :error="errors.password"
        />

        <CustomInput
          v-model="form.password_confirmation"
          label="Potwierdź nowe hasło"
          type="password"
          name="password_confirmation"
          :error="errors.password_confirmation"
        />

        <button
          type="submit"
          class="bg-primary text-white font-bold py-3 rounded-lg mt-4 duration-200 transition-colors"
        >
          Zmień hasło
        </button>
      </form>
    </div>
  </div>
</template>
