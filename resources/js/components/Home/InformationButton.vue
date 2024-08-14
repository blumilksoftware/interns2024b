<script setup lang="ts">
import InformationModalWindow from '@/components/Home/InformationModalWindow.vue'
import { ref } from 'vue'

const isInformationVisible = ref(false)
const setVisible = () => {
  isInformationVisible.value = true
}
</script>


<template>
  <div class="flex h-12 justify-center items-center">
    <button class="text-primary text-[1.5rem] font-bold text-center hover:text-[1.55rem] duration-200" @click="setVisible">INFORMACJE O KONKURSIE</button>
  </div>
  <InformationModalWindow v-model:is-visible="isInformationVisible" />
</template>
