<script setup lang="ts">
import InformationModalWindow from '@/components/Home/InformationModalWindow.vue'
import { ref } from 'vue'

const isInformationVisible = ref(false)
</script>

<template>
  <div class="flex h-12 justify-center items-center">
    <button class="text-primary text-2xl font-bold duration-200 hover:text-primary-800 hover:scale-105" @click="isInformationVisible = true">
      INFORMACJE O KONKURSIE
    </button>
  </div>
  <InformationModalWindow v-model:is-visible="isInformationVisible" />
</template>
