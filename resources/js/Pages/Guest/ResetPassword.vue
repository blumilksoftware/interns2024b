<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import CustomInput from '@/components/Common/CustomInput.vue'
import { Head } from '@inertiajs/vue3'
import type { PageProps } from '@/Types/PageProps'
import DisplayError from '@/components/Common/DisplayError.vue'
import { inject, onMounted, type Ref, ref } from 'vue'
import EyeDynamicIcon from '@/components/Icons/EyeDynamicIcon.vue'
import type Header from '@/components/Common/Header.vue'

const headerRef = inject<Ref<InstanceType<typeof Header>>>('header')
onMounted(()=>{
  if (headerRef)
    headerRef.value.titleRef = 'Zmiana hasła'
})
const props = defineProps<{
  token: string
} & PageProps>()
const isVisiblePassword = ref(false)
const isVisibleConfirmPassword = ref(false)
const form = useForm({
  password: '',
  password_confirmation: '',
  token: props.token,
})

function submit() {
  form.post('/auth/password/reset')}
</script>

<template>
  <Head>
    <title>{{ headerRef?.titleRef }}</title>
    <meta name="Zmiana hasła" content="Zmiana hasła">
  </Head>
  <div class="sm:p-6 sm:pt-12 size-full flex flex-col items-center justify-center sm:h-fit gap-6">
    <div class="text-md font-medium leading-6 text-gray-900 px-[5vw] text-center flex flex-col gap-1">
      <p>Wprowadź nowe hasło z którego będziesz korzystać w aplikacji.</p>
    </div>
    <form class="p-6 gap-4 flex flex-col size-full sm:rounded-lg sm:max-w-lg sm:h-fit" @submit.prevent="submit">
      <CustomInput v-model="form.password" label="Nowe hasło" :error="errors?.password" name="password" :type="isVisiblePassword ? 'text' : 'password'">
        <div class="cursor-pointer text-primary/70" @click="isVisiblePassword=!isVisiblePassword">
          <EyeDynamicIcon :is-opened="isVisiblePassword" />
        </div>
      </CustomInput>
      <CustomInput v-model="form.password_confirmation" label="Powtórz hasło" :error="errors?.password_confirmation" name="password_confirmation" :type="isVisibleConfirmPassword ? 'text' : 'password'">
        <div class="cursor-pointer text-primary/70" @click="isVisibleConfirmPassword=!isVisibleConfirmPassword">
          <EyeDynamicIcon :is-opened="isVisibleConfirmPassword" />
        </div>
      </CustomInput>
      <DisplayError :error="errors?.email ? 'Link jest niepoprawny' : ''" />
      <button :disabled="form.processing" type="submit" class="bg-primary text-white font-bold py-3 px-4 rounded-lg disabled:bg-primary/70">
        Zmień hasło
      </button>
    </form>
  </div>
</template>
