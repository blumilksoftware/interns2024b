<script setup lang="ts">
defineProps<{ text:string }>()
</script>

<template>
  <div class="w-full flex">
    <div class="flex justify-center flex-1 caution mirror-bg-x" />
    
    <span class="text-white font-bold px-5 bg-black">{{ text }}</span>
    
    <div class="flex justify-center flex-1 caution" />
  </div>
</template>

<style scoped>
.caution {
  background: linear-gradient(135deg, black 33%, yellow 33%, yellow 66%, black 66%);
  background-size: 100px 50px;
}
.mirror-bg-x {
  background-position: right center;
}
</style>
