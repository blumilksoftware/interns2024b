<script lang="ts" setup>

import InformationButton from '@/components/Home/InformationButton.vue'

</script>

<template>
  <section class="w-full flex flex-col overflow-hidden">
    <div class="inset-0 top-16  h-88 lg:h-screen lg:top-0 bg-white/30 absolute -z-10" />
    <div data-name="left" class="flex flex-col justify-center w-full gap-10 p-10 pb-16">
      <img class="h-40 object-contain" src="@/../assets/header-logo.png" alt="logo nagłówka">
      <InformationButton />
    </div>

    <div data-name="right" class="p-16 flex flex-col size-full">
      <h1 class="text-center text-[1.5rem] font-bold">SPONSORZY</h1>
      <div class="mx-auto mt-10 grid max-w-lg grid-cols-4 items-center gap-x-8 gap-y-10 sm:grid-cols-6">
        <img class="cursor-pointer duration-200 hover:scale-110 col-span-2 max-h-12 w-full object-contain" src="https://tailwindui.com/img/logos/158x48/transistor-logo-gray-900.svg" alt="Transistor" width="158" height="48">
        <img class="cursor-pointer duration-200 hover:scale-110 col-span-2 max-h-12 w-full object-contain" src="https://tailwindui.com/img/logos/158x48/reform-logo-gray-900.svg" alt="Reform" width="158" height="48">
        <img class="cursor-pointer duration-200 hover:scale-110 col-span-2 max-h-12 w-full object-contain" src="https://tailwindui.com/img/logos/158x48/tuple-logo-gray-900.svg" alt="Tuple" width="158" height="48">
        <img class="cursor-pointer duration-200 hover:scale-110 col-span-2 max-h-12 w-full object-contain sm:col-start-2" src="https://tailwindui.com/img/logos/158x48/savvycal-logo-gray-900.svg" alt="SavvyCal" width="158" height="48">
        <img class="cursor-pointer duration-200 hover:scale-110 col-span-2 col-start-2 max-h-12 w-full object-contain sm:col-start-auto" src="https://tailwindui.com/img/logos/158x48/statamic-logo-gray-900.svg" alt="Statamic" width="158" height="48">
      </div>
    </div>
  </section>
</template>
