<script setup lang="ts">
import BackgroundEffect from '@/components/Home/BackgroundEffect.vue'
</script>

<template>
  <div class="absolute opacity-50 -z-50">
    <BackgroundEffect />
  </div>
  <div class="bg-primary-600/10 size-full fixed top-0 left-0 -z-40" />
  <!-- <div class="">
    <header class="top-0 w-full py-3 px-6 gap-y-4 gap-x-6 flex justify-evenly items-center bg-white/30 backdrop-blur-md z-10 duration-200">
      <a class="font-bold" href="#">Uczniowie</a>
      <span class="text-gray-300">|</span>
      <a class="font-bold" href="#">Lista&nbsp;szkół</a>
      <span class="text-gray-300">|</span>
      <a class="font-bold" href="/quizzes">Testy</a>
      <div class="w-full" />
      <a class="font-bold" href="#">Wyloguj&nbsp;się</a>
    </header>
    <header class="top-0 w-full py-2.5 px-6 gap-y-4 gap-x-6 flex justify-evenly items-center bg-white/30 backdrop-blur-md shadow z-10 duration-200">
      <slot name="subheader" />
    </header> 
  </div> -->
</template>
