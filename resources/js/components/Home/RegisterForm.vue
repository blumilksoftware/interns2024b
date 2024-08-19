<script lang="ts" setup>
import {ref} from 'vue'
import Checkbox from '@/components/Common/Checkbox.vue'
import Searchbar from '@/components/Common/Searchbar.vue'

// example options for schools school
const searchbarOptions = ref([
  {id: 0, option: 'Szkoła #1'},
  {id: 1, option: 'Szkoła #2'},
  {id: 2, option: 'Szkoła #3'},
  {id: 3, option: 'Szkoła #4'},
  {id: 4, option: 'Szkoła #5'},
  {id: 5, option: 'Szkoła #6'},
  {id: 6, option: 'Szkoła #7'},
  {id: 7, option: 'Szkoła #8'},
])
</script>

<template>
  <form class="row-start-1 col-start-1 space-y-6" method="POST" @submit.prevent="">
    <div class="flex flex-col gap-6 sm:flex-row">
      <div class="w-full">
        <label for="name" class="text-sm font-medium leading-6 text-gray-900">Imię</label>
        <div class="mt-2 w-full">
          <input id="name" required name="name" type="text"
                 class="duration-200 ring-inset outline-none focus:ring focus:ring-primary bg-white/30 rounded-lg w-full p-3 text-gray-900 ring-2 ring-primary/30 placeholder:text-gray-400"
          >
        </div>
      </div>

      <div class="w-full">
        <label for="surname" class="text-sm font-medium leading-6 text-gray-900">Nazwisko</label>
        <div class="mt-2 w-full">
          <input id="surname" required name="surname" type="text"
                 class="duration-200 ring-inset outline-none focus:ring focus:ring-primary bg-white/30 rounded-lg w-full p-3 text-gray-900 ring-2 ring-primary/30 placeholder:text-gray-400"
          >
        </div>
      </div>
    </div>

    <div>
      <div class="flex items-center justify-between">
        <label for="search-schools" class="block text-sm font-medium leading-6 text-gray-900">Szkoła</label>
      </div>
      <Searchbar :options="searchbarOptions" />
    </div>

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
    </div>

    <div class="mx-2 mt-4 flex flex-row items-center gap-4">
      <Checkbox />
      <p class="w-fit text-sm text-gray-500">
        Akceptuję
        <a href="#" class="font-semibold leading-6 text-primary hover:primary">regulamin</a> i
        <a href="#" class="font-semibold leading-6 text-primary hover:primary">politykę prywatności</a>
      </p>
    </div>

    <div>
      <button type="submit"
              class="
              rounded-lg text-md flex w-full justify-center bg-primary p-3 font-bold text-white transition hover:bg-primary-950
              focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary"
      >
        Zarejestruj się
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
