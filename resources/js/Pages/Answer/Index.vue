<script setup lang="ts">
import {type Answer} from '@/Types/Answer'
import Show from './Show.vue'

const props = defineProps<{
  answers: Answer[]
}>()
</script>

<template>
  <h1 v-if="props.answers.length > 0" class="text-xl">ANSWERS</h1>
  <div v-for="item in props.answers" :key="item.id">
    <Show :answer="item" />
  </div>
</template>
