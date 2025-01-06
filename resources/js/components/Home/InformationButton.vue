<script setup lang="ts">
import { ref } from 'vue'
import ModalWindow from '@/components/Common/ModalWindow.vue'

const isInformationVisible = ref(false)
</script>

<template>
  <div class="flex h-12 justify-center items-center">
    <button
      class="text-primary text-2xl font-bold duration-200 hover:text-primary-bright hover:scale-105"
      @click="isInformationVisible = true"
    >
      INFORMACJE O KONKURSIE
    </button>
  </div>

  <ModalWindow
    v-model="isInformationVisible"
    title="Informacje o konkursie"
  >
    Lorem ipsum dolor sit amet consectetur adipisicing elit.
    Esse corrupti provident voluptas ducimus neque libero reprehenderit eos voluptate quisquam minus.
    Dolorum quos animi fugit blanditiis repellendus voluptas sequi ex ea.
  </ModalWindow>
</template>
