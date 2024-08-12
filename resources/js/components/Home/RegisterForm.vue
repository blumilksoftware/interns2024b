<script lang="ts" setup>
import Checkbox from '@/components/Common/Checkbox.vue'
</script>

<template>
  <form class="row-start-1 col-start-1 space-y-6" method="POST" @submit.prevent="submitForm">
    <div class="flex flex-row gap-6">
      <div class="w-full">
        <label for="name" class="text-sm font-medium leading-6 text-gray-900">Name</label>
        <div class="mt-2 w-full">
          <input id="name" required name="name" type="text"
                 class="ring-inset outline-none focus:ring focus:ring-primary bg-white/30 rounded-lg w-full p-3 text-gray-900 ring-2 ring-primary/30 placeholder:text-gray-400 sm:text-sm sm:leading-6"
          >
        </div>
      </div>

      <div class="w-full">
        <label for="surname" class="text-sm font-medium leading-6 text-gray-900">Surname</label>
        <div class="mt-2 w-full">
          <input id="surname" required name="surname" type="text"
                 class="ring-inset outline-none focus:ring focus:ring-primary bg-white/30 rounded-lg w-full p-3 text-gray-900 ring-2 ring-primary/30 placeholder:text-gray-400 sm:text-sm sm:leading-6"
          >
        </div>
      </div>
    </div>
    <div>
      <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email address</label>
      <div class="mt-2">
        <input id="email" required name="email" type="email" autocomplete="email"
               class="ring-inset outline-none focus:ring focus:ring-primary bg-white/30 rounded-lg w-full p-3 text-gray-900 ring-2 ring-primary/30 placeholder:text-gray-400 sm:text-sm sm:leading-6"
        >
      </div>
    </div>

    <div>
      <div class="flex items-center justify-between">
        <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Password</label>
      </div>
      <div class="mt-2">
        <input id="password" required name="password" type="password" autocomplete="current-password"
               class="ring-inset outline-none focus:ring focus:ring-primary bg-white/30 rounded-lg w-full p-3 text-gray-900 ring-2 ring-primary/30 placeholder:text-gray-400 sm:text-sm sm:leading-6"
        >
      </div>
    </div>

    <div>
      <div class="flex items-center justify-between">
        <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Choose a school</label>
      </div>
      <div class="mt-2">
        <select id="cars" name="cars"
                class="bg-white/30 rounded-lg transition block w-full p-3 text-gray-900 ring-2 ring-inset ring-primary/30 placeholder:text-gray-400 focus:ring-2 focus:ring-primary"
        >
          <option value="School #1">School #1</option>
          <option value="School #2">School #2</option>
          <option value="School #3">School #3</option>
          <option value="School #4">School #4</option>
        </select>
      </div>
    </div>

    <div class="mx-2 mt-4 flex flex-row items-center gap-4">
      <Checkbox />
      <p class="w-fit text-sm text-gray-500">
        By checking the box you're accepting the terms of service and
        <a href="#" class="font-semibold leading-6 text-primary hover:primary">regulations</a>
      </p>
    </div>

    <div>
      <button type="submit"
              class="
              rounded-lg text-md flex w-full justify-center bg-primary p-3 font-bold text-white transition hover:bg-primary-950
              focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary"
      >
        Continue
      </button>
    </div>
  </form>
</template>
