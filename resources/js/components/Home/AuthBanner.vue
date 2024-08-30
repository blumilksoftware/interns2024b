<script setup lang="ts">

import { ref, onMounted, onUnmounted, type Ref } from 'vue'
import { inject } from 'vue'

const isLoginRef: Ref<any, any> | undefined = inject('isLoginRef')
const isHidden = ref(false)

function setBannerVisibility() {
  isHidden.value = window.scrollY / 100 >= 5
}
onMounted(() => window.addEventListener('scroll', setBannerVisibility))
onUnmounted(() => window.removeEventListener('scroll', setBannerVisibility))

function gotoAuthSection() {
  window.scrollTo({
    top: window.innerHeight,
    behavior: 'smooth',
  })
}

function gotoRegisterSection() {
  gotoAuthSection()
  if (isLoginRef)
    isLoginRef.value.isLogin = false
}

function gotoLoginSection() {
  gotoAuthSection()
  if (isLoginRef)
    isLoginRef.value.isLogin = true
}

</script>

<template>
  <div>
    <div class="h-16 lg:hidden" />
    <div :class="{ '-translate-y-full': isHidden }"
         class="lg:hidden top-0 duration-200 justify-evenly z-10 backdrop-blur-md bg-white/30 lg:bg-white fixed w-full flex items-center gap-x-6 px-6 py-2.5"
    >
      <div class="justify-center flex-1 hidden lg:flex" />
      <div class="hidden lg:justify-center flex-1 xs:flex">
        <p class="font-bold text-sm leading-6 text-gray-900">Testy</p>
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
