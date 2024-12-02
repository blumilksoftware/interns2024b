<script lang="ts" setup>
import InformationButton from '@/components/Home/InformationButton.vue'
</script>

<template>
  <section class="w-full flex flex-col items-center gap-10 p-10">
    <img class="h-40 object-contain" src="@/../assets/header-logo.png" alt="logo nagłówka">

    <InformationButton />

    <h1 class="text-center text-[1.5rem] font-bold mt-20">SPONSOR</h1>

    <img
      class="w-fit rounded cursor-pointer duration-200 hover:scale-110 object-contain max-h-48"
      src="https://www.tauron.pl/-/media/layout/logo_tlo.ashx"
      alt="Tauron"
    >
  </section>
</template>
