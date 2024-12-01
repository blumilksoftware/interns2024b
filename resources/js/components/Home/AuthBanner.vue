<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import LogoIcon from '@/components/Icons/LogoIcon.vue'

const isHidden = ref(false)
const props = defineProps<{isLogin?:boolean}>()
const emit = defineEmits<{scrollToAuth:[isLogin:boolean]}>()
const isLoginRef = ref<boolean>(props.isLogin)
const banner = ref()
const height = ref(0)

onMounted(() => {
  height.value = banner.value.offsetHeight
})

function setBannerVisibility() {
  isHidden.value = window.scrollY / 100 >= 5
}
onMounted(() => window.addEventListener('scroll', setBannerVisibility))
onUnmounted(() => window.removeEventListener('scroll', setBannerVisibility))

function gotoAuthSection() {
  emit('scrollToAuth', isLoginRef.value)
}

function gotoRegisterSection() {
  isLoginRef.value = false
  gotoAuthSection()
}

function gotoLoginSection() {
  isLoginRef.value = true
  gotoAuthSection()
}
</script>

<template>
  <div class="flex h-fit">
    <div :style="{height: `${height}px`}" class="lg:hidden" />
    <div ref="banner" :class="{ '-translate-y-full': isHidden }"
         class="lg:hidden flex-1 duration-200 justify-evenly z-10 backdrop-blur-md bg-white/30 lg:bg-white fixed w-full flex items-center gap-x-6 px-6 py-2.5"
    >
      <div class="justify-center flex-1 hidden lg:flex" />
      <div class="hidden lg:justify-center flex-1 xs:flex">
        <LogoIcon />
      </div>
      <div class="flex justify-end flex-1 gap-6 items-center">
        <b><button class="text-sm text-primary" @click="gotoLoginSection">Logowanie</button></b>
        <button
          class="flex-none rounded-lg bg-primary px-3.5 py-3 text-sm font-bold text-white shadow-sm hover:bg-primary-dark duration-200 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-900"
          @click="gotoRegisterSection"
        >
          Rejestracja
        </button>
      </div>
    </div>
  </div>
</template>
