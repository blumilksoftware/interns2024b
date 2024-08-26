<script setup lang="ts">
import CheckDynamicIcon from '@/components/Icons/CheckDynamicIcon.vue'
defineProps<{id:number, isCorrect:boolean}>()
const text = defineModel<string>('text')
const emit = defineEmits(['checkClick', 'update:answer'])

function checkClick(answerId:number) {
  emit('checkClick', answerId)
}


</script>

<template>
  <div class="flex mb-1">
    <textarea
      v-model="text"
      class="h-auto ring ring-primary/30 p-2 rounded"
      rows="2" cols="80" :class="{'ring-green-500': isCorrect}"
    />
    <div class="cursor-pointer h-size flex items-center p-2" @click="checkClick(id)">
      <CheckDynamicIcon class="size-8" :is-correct="isCorrect" />
    </div>
  </div>
</template>

