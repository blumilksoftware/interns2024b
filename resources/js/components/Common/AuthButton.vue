<script setup lang="ts">
const props = defineProps<{isLogin: boolean}>()
const emit = defineEmits<(e: 'update:isLogin', value: boolean) => void>()
</script>

<template>
  <button
    class="flex p-2 relative w-full cursor-pointer border-2 border-primary/30 rounded-xl bg-white/30 outline-none focus:border-primary/60 duration-200 transition-colors"
    aria-label="Przełącznik rejestracji i logowania"
    @click="emit('update:isLogin', !props.isLogin)"
  >
    <span
      :class="{ 'translate-x-full' : props.isLogin }"
      class="left-2 w-[calc(50%-.5rem)] absolute h-[calc(100%-1rem)] rounded-md bg-primary duration-200"
    />
    <span :class="{ 'text-white': !props.isLogin }" class="z-10 w-full text-center duration-200 py-3 text-primary font-bold">Rejestracja</span>
    <span :class="{ 'text-white': props.isLogin }" class="z-10 w-full text-center duration-200 py-3 text-primary font-bold">Logowanie</span>
    <div aria-live="assertive" class="sr-only">
      {{ props.isLogin ? 'Logowanie' : 'Rejestracja' }}
    </div>
  </button>
</template>
