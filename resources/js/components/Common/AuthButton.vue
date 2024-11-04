<script setup lang="ts">
const props = defineProps<{ isLogin: boolean }>()
const emit = defineEmits<(e: 'update:isLogin', value: boolean) => void>()

const handleCheckboxChange = () => {
  emit('update:isLogin', !props.isLogin)
}
</script>

<template>
  <label class="flex p-2 relative w-full cursor-pointer border-2 border-primary/30 rounded-xl  bg-white/30">
    <input
      type="checkbox"
      class="sr-only"
      @change="handleCheckboxChange"
    >

    <span
      :class="{ 'translate-x-full' : props.isLogin}"
      class="left-2 w-[calc(50%-.5rem)] absolute h-[calc(100%-1rem)] rounded-md bg-primary duration-200"
    />

    <span
      :class="{ 'text-white': !props.isLogin}"
      class="z-10 w-full text-center duration-200 py-3 text-primary font-bold"
    >
      Rejestracja
    </span>

    <span
      :class="{ 'text-white': props.isLogin }"
      class="z-10 w-full text-center duration-200 py-3 text-primary font-bold"
    >
      Logowanie
    </span>
  </label>
</template>
