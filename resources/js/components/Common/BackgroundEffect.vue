<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'

defineProps<{white?: boolean}>()

const margin = ref(0)
function setBannerVisibility() {
  const scrollHeight = window.document.documentElement.scrollHeight / 2
  margin.value = window.scrollY / scrollHeight * 10
}
onMounted(() => window.addEventListener('scroll', setBannerVisibility))
onUnmounted(() => window.removeEventListener('scroll', setBannerVisibility))
</script>

<template>
  <div
    v-if="white"
    class="inset-0 bg-white/60 fixed -z-10"
  />

  <div
    :style="{marginTop: `${margin}rem`}"
    class="fixed blur-2xl size-7/12 -z-10 opacity-70"
  >
    <div
      class="
      size-full
      bg-gradient-to-r
      from-[#ff80b5]
      to-[#373ff4]
      opacity-30
    "
      style="
      clip-path: polygon(
        75% 42%, 98% 74%, 100% 35%, 93% 0%, 86% 0%, 75% 27%, 59% 55%, 50% 57%, 47% 44%, 48% 17%, 25% 54%, 0% 28%, 12% 75%, 25% 54%, 67% 100%, 75% 42%
      );"
    />
  </div>
</template>
