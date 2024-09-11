<script setup lang="ts">
import { ref } from 'vue'
import CustomInput from '@/components/Common/CustomInput.vue'
import EyeDynamicIcon from '@/components/Icons/EyeDynamicIcon.vue'

defineProps<{ error?: string }>()
const model = defineModel<string>()
const isVisible = ref<boolean>(false)
</script>

<template>
  <CustomInput v-model="model" :error="error" label="Hasło" name="password" :type="isVisible ? 'text' : 'password'">
    <div class="cursor-pointer text-primary/70" @click="isVisible=!isVisible">
      <EyeDynamicIcon :is-opened="isVisible" />
    </div>
  </CustomInput>
</template>
