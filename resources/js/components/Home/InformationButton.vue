<script setup lang="ts">
import InformationModalWindow from '@/components/Home/InformationModalWindow.vue'
import { ref } from 'vue'

const isInformationVisible = ref(false)
</script>

<template>
  <div class="flex h-12 justify-center items-center">
    <button class="text-primary text-2xl font-bold text-center hover:text-hovered-inf-btn duration-200" @click="isInformationVisible = true">
      INFORMACJE O KONKURSIE
    </button>
  </div>
  <InformationModalWindow v-model:is-visible="isInformationVisible" />
</template>
