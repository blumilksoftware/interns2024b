<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { type Errors } from '@inertiajs/core'
import AuthButton from '@/components/Common/AuthButton.vue'
import RegisterForm from '@/components/Home/RegisterForm.vue'
import LoginForm from '@/components/Home/LoginForm.vue'

const authSection = ref()

const isLogin = ref(false)
const authSectionElement = ref<HTMLElement | undefined>() 
defineExpose({ isLogin, authSectionElement })
const props = defineProps<{ errors: Errors }>()

onMounted(() => {
  const { height } = authSection.value.getBoundingClientRect()
  authSection.value.style.height = `${height}px`
})
</script>

<template>
  <section
    ref="authSectionElement"
    class="w-full flex flex-col items-center overflow-hidden backdrop-blur bg-white/30 lg:backdrop-blur-none lg:bg-transparent"
  >
    <div class="w-full flex flex-col gap-10 max-w-lg px-5 py-20">
      <AuthButton v-model:is-login="isLogin" />

      <div
        ref="authSection"
        class="grid"
      >
        <Transition :name="isLogin ? 'slide-right' : 'slide-left'">
          <LoginForm
            v-if="isLogin"
            :errors="props.errors"
          />

          <RegisterForm
            v-else
            :errors="props.errors"
          />
        </Transition>
      </div>
    </div>
  </section>
</template>
