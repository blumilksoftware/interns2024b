<script setup lang="ts">

import { ref, defineExpose } from 'vue'
import AuthButton from '@/components/Common/AuthButton.vue'
import RegisterForm from '@/components/Home/RegisterForm.vue'
import LoginForm from '@/components/Home/LoginForm.vue'
import type {School} from '@/Types/School'

const isLogin = ref(false)
defineExpose({ isLogin })

const { errors, schools } = defineProps<{
  errors: Record<string, string>
  schools: School[]
}>()

</script>

<template>
  <section class="w-full flex flex-col items-center overflow-hidden backdrop-blur bg-white/30 lg:backdrop-blur-none lg:bg-transparent">
    <div class="w-full flex flex-col gap-10 max-w-lg px-5 py-20">
      <AuthButton v-model:is-login="isLogin" />
      <div class="grid">
        <Transition :name="isLogin ? 'slide-right' : 'slide-left'">
          <LoginForm v-if="isLogin" key="on" :errors="errors" />
          <RegisterForm v-else key="off" :errors="errors" :schools="schools" />
        </Transition>
      </div>
    </div>
  </section>
</template>

<style>
.slide-left-enter-active,
.slide-right-enter-active,
.slide-left-leave-active,
.slide-right-leave-active {
  transition: all .3s;
  transition-timing-function: cubic-bezier(0,.5,.5,1);
}

.slide-left-enter-from,
.slide-right-leave-to {
    transform: translateX(-100%);
    opacity: 0;
}

.slide-left-leave-to,
.slide-right-enter-from {
    transform: translateX(100%);
    opacity: 0;
}
</style>
