<script lang="ts" setup>
import { computed } from 'vue'
import { nanoid } from 'nanoid'
import { useForm } from '@inertiajs/vue3'
import { type Errors } from '@inertiajs/core'
import Checkbox from '@/components/Common/Checkbox.vue'
import Searchbar from '@/components/Common/Searchbar.vue'
import CustomInput from '@/components/Common/CustomInput.vue'
import PasswordInput from '@/components/Common/PasswordInput.vue'
import { type School } from '@/Types/School'

const props = defineProps<{ errors:Errors, schools:School[] }>()
const filteredSchools = computed(() => props.schools.toSorted((a, b) => (a.city + a.name).localeCompare(b.city + b.name)))
const filteredSchoolOptions = computed(() =>
  filteredSchools.value.map((school:School):Option & School =>
    ({...school, key: nanoid(), title: school.city, text: school.name }),
  ),
)
const form = useForm({
  name: '',
  surname: '',
  email: '',
  password: '',
  school_id: '',
})

function submit() {
  form.post('/auth/register', { preserveScroll: true, preserveState: true })
}
</script>

<template>
  <form class="row-start-1 col-start-1 space-y-6" @submit.prevent="submit">
    <div class="flex flex-col gap-6 sm:flex-row">
      <CustomInput v-model="form.name" label="Imię" :error="errors.name" name="name" type="name" />
      <CustomInput v-model="form.surname" label="Nazwisko" :error="errors.surname" name="surname" type="surname" />
    </div>

    <Searchbar
      label="Szkoła"
      aria-label="Poszukiwanie szkół. Otwiera listę szkół"
      :options="filteredSchoolOptions"
      :error="errors.school_id"
      @change="school => form.school_id = school.id.toString()"
    />

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
        :disabled="form.processing"
        type="submit"
        class="rounded-lg text-md flex w-full justify-center bg-primary p-3 font-bold text-white
        transition hover:bg-primary-950 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary
        disabled:opacity-70"
      >
        Zarejestruj się
      </button>
    </div>
  </form>
</template>
