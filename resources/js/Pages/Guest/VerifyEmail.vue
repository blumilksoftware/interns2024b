<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import { inject, onMounted, type Ref } from 'vue'

const form = useForm({})
const titleRef = inject<Ref<string>>('titleRef')

onMounted(()=>{
  if (titleRef)
    titleRef.value = 'Zweryfikuj e-mail'
})

function send() {
  form.post('/email/verification-notification')
}

</script>

<template>
  <Head :title="titleRef" />

  <div class="sm:p-6 sm:pt-12 size-full flex flex-col items-center justify-center sm:h-fit gap-6">
    <div class="text-md font-medium leading-6 text-gray-900 px-[5vw] text-center flex flex-col gap-1">
      <p>Dziękujemy za rejestrację! Wysłaliśmy wiadomość z linkiem aktywacyjnym na Twój adres e-mail.</p>
      <p>Sprawdź skrzynkę odbiorczą, a jeśli nie widzisz wiadomości, zajrzyj do folderu SPAM.</p>
      <p>Kliknij w link aktywacyjny, aby dokończyć proces rejestracji.</p>
    </div>
    <div class="p-6 gap-6 flex flex-col size-full sm:max-w-lg sm:h-fit">
      <button :disabled="form.processing" class="bg-primary text-white font-bold py-3 px-4 rounded-lg disabled:bg-primary/70" @click="send">
        Wyślij ponownie link weryfikacyjny
      </button>
    </div>
  </div>
</template>
