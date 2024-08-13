<script setup lang="ts">
import {type Question} from '@/Types/Question'
import Answers from '../Answer/Index.vue'
import {router} from '@inertiajs/vue3'

const props = defineProps<{
    question: Question
  }>()
</script>

<template>
  <div class="ml-8 border-2">
    <div>id: {{ props.question.id }}</div>
    <div>text: {{ props.question.text }}</div>
    <div>created: {{ props.question.createdAt }}</div>
    <div>updated: {{ props.question.updatedAt }}</div>
    <div>locked: {{ props.question.locked }}</div>
    <div>correct: {{ props.question.correct ?? "null" }}</div>
    <button class="font-bold block" @click="router.delete(`/questions/${props.question.id}`)">DELETE</button>

    <Answers :answers="props.question.answers" />
  </div>
</template>
