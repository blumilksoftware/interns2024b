<script setup lang="ts">

function submitForm() {
  // ...  
}
</script>

<template>
  <form class="row-start-1 col-start-1 space-y-6" method="POST" @submit.prevent="submitForm">
    <div>
      <label for="email" class="block text-sm font-medium leading-6 text-gray-900">E-mail</label>
      <div class="mt-2">
        <input id="email" required name="email" type="email" autocomplete="email"
               class="duration-200 ring-inset outline-none focus:ring focus:ring-primary bg-white/30 rounded-lg w-full p-3 text-gray-900 ring-2 ring-primary/30 placeholder:text-gray-400"
        >
      </div>
    </div>

    <div>
      <div class="flex items-center justify-between">
        <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Hasło</label>
      </div>
      <div class="mt-2">
        <input id="password" required name="password" type="password" autocomplete="current-password"
               class="duration-200 ring-inset outline-none focus:ring focus:ring-primary bg-white/30 rounded-lg w-full p-3 text-gray-900 ring-2 ring-primary/30 placeholder:text-gray-400"
        >
      </div>
      <div class="mt-2">
        <a href="#" class="duration-200 font-semibold leading-6 text-primary hover:text-primary-950 text-sm">Nie pamiętam hasła</a>
      </div>
    </div>
    
    <div>
      <button type="submit" class="
              rounded-lg text-md flex w-full justify-center bg-primary p-3 font-bold text-white transition hover:bg-primary-950
              focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary
            "
      >
        Zaloguj się
      </button>
    </div>
  </form>
</template>

