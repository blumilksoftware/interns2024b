<script setup lang="ts">
import { ref } from 'vue'
import CustomInput from './CustomInput.vue'
import EyeDynamicIcon from '../Icons/EyeDynamicIcon.vue'
defineProps<{ error?: string }>()
const model = defineModel<string>('')
const isVisible = ref<boolean>(false)
</script>

<template>
  <CustomInput v-model="model" :error="error" label="Hasło" name="password" :type="isVisible ? 'text' : 'password'">
    <div class="cursor-pointer" @click="isVisible=!isVisible">
      <EyeDynamicIcon :is-opened="isVisible" />
    </div>
  </CustomInput>
</template>
