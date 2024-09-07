<script lang="ts" setup>

import { ref } from 'vue'
import Checkbox from '@/components/Common/Checkbox.vue'
import Searchbar from '@/components/Common/Searchbar.vue'
import { type School } from '@/Types/School'
import CustomInput from '../Common/CustomInput.vue'
import { Request } from '@/scripts/request'
import PasswordInput from '../Common/PasswordInput.vue'

const { errors, schools } = defineProps<{
  errors: Record<string, string>
  schools: School[]
}>()

const form = ref({
  name: '',
  surname: '',
  email: '',
  password: '',
  school_id: '',
})

const request = new Request()

function submit() {
  request.sendRequest('/auth/register', {method: 'post', data: form.value, preserveScroll: true, preserveState: true})
}
</script>

<template>
  <form class="row-start-1 col-start-1 space-y-6" @submit.prevent="submit">
    <div class="flex flex-col gap-6 sm:flex-row">
      <CustomInput v-model="form.name" label="Imię" :error="errors.name" name="name" type="name" />
      <CustomInput v-model="form.surname" label="Nazwisko" :error="errors.surname" name="surname" type="surname" />
    </div>

    <div>
      <div class="flex items-center justify-between">
        <label for="school_id" class="block text-sm font-medium leading-6 text-gray-900">Szkoła</label>
      </div>
      <Searchbar :schools="schools" @change="school => form.school_id = school" />
      <div v-if="errors.school_id" class="text-red">{{ errors.school_id }}</div>
    </div>

    <CustomInput v-model="form.email" label="E-mail" :error="errors.email" name="email" type="email" />
    <PasswordInput v-model="form.password" :error="errors.password" />

    <label class="mx-2 mt-4 flex flex-row items-center gap-4">
      <Checkbox />
      <p class="w-fit text-sm text-gray-500">
        Akceptuję
        <a href="#" class="font-semibold leading-6 text-primary hover:primary">regulamin</a> i
        <a href="#" class="font-semibold leading-6 text-primary hover:primary">politykę prywatności</a>
      </p>
    </label>

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

<style scoped>
::-webkit-scrollbar {
  width: 10px;
}

::-webkit-scrollbar-track {
  background: #ffffff4c;
}

/* Handle */
::-webkit-scrollbar-thumb {
  background: #262c8926;
  border-radius: 1rem;
  border: 2px solid transparent;
  background-clip: content-box;
}

::-webkit-scrollbar-thumb:hover {
  background: #262c894c;
  border: 2px solid transparent;
  background-clip: content-box;
}
</style>
