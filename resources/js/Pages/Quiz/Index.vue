<script setup lang="ts">
import Show from './Show.vue'
import {type Quiz} from '@/Types/Quiz'

const props = defineProps<{
  quizzes: Quiz[]
}>()
</script>

<template>
  <h1 v-if="props.quizzes.length > 0" class="text-xl">QUIZZES</h1>
  <div v-for="item in props.quizzes" :key="item.id">
    <Show :quiz="item" />
  </div>
</template>
