<script setup lang="ts">

import { ref, onMounted, onUnmounted } from 'vue'

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
        <svg class="size-10" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M7.02484 14.3074C6.60832 14.3074 6.3744 13.828 6.63076 13.4997L15.3851 2.28913C15.7308 1.84642 16.4312 2.22227 16.2534 2.75511L13.6884 10.4434C13.6668 10.5082 13.715 10.575 13.7832 10.575H16.4951C16.9018 10.575 17.1384 11.0347 16.902 11.3656L9.30015 22.0091C8.96935 22.4723 8.24533 22.1111 8.41638 21.5683L10.6633 14.4375C10.6836 14.373 10.6355 14.3074 10.5679 14.3074H7.02484Z" fill="#262C89" /><path fill-rule="evenodd" clip-rule="evenodd" d="M12.2545 17.8224V6.2803L15.3847 2.28313C15.731 1.84101 16.4307 2.21751 16.2525 2.75005L13.6885 10.4136C13.6668 10.4784 13.715 10.5453 13.7834 10.5453H16.4935C16.9005 10.5453 17.137 11.0056 16.9 11.3365L12.2545 17.8224Z" fill="#6566C2" />
        </svg>
      </div>
      <div class="flex justify-end flex-1 gap-6 items-center">
        <b><button class="text-sm text-primary" @click="gotoLoginSection">Logowanie</button></b>
        <button
          class="flex-none rounded-lg bg-primary px-3.5 py-3 text-sm font-bold text-white shadow-sm hover:bg-primary-950 duration-200 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-900"
          @click="gotoRegisterSection"
        >
          Rejestracja
        </button>
      </div>
    </div>
  </div>
</template>
