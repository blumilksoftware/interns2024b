<script setup lang="ts">
// import { ref } from 'vue'

</script>

<template>
  <div class="h-screen w-screen !bg-cyan-500 -z-10 absolute top-0 left-0" />
  <!-- TODO: add that beutiful background once my pull reqest gets merged -->
  <header class="p-4 flex gap-4">
    <a href="#">Uczniowie</a>
    <a href="#">Lista&nbsp;szkół</a>
    <a href="/admin/quizzes">Testy</a>
    <div class="w-full" />
    <a>Wyloguj&nbsp;się</a>
  </header>
  <header class="p-4 flex gap-4">
    <slot name="subheader" />
  </header>
  <main class="flex gap-4 p-4 min-h-full">
    <div class="items flex flex-col">
      <slot name="items" />
    </div>
    <div class="size-full">
      <slot name="selectedItemContent" />
    </div>
  </main>
</template>
