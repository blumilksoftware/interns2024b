<script setup lang="ts">
  import {type Answer} from "@/Types/Answer";
  import {router} from "@inertiajs/vue3";

  const props = defineProps<{
    answer: Answer
  }>()
</script>

<template>
  <div class="ml-8 border-2">
    <div>id: {{props.answer.id}}</div>
    <div>text: {{props.answer.text}}</div>
    <div>create: {{props.answer.createdAt}}</div>
    <div>updated: {{props.answer.updatedAt}}</div>
    <div>locked: {{props.answer.locked}}</div>
    <div>is correct: {{props.answer.correct}}</div>

    <button class="font-bold block" @click="router.post(`/answers/${props.answer.id}/correct`)">Correct</button>
    <button class="font-bold block" @click="router.delete(`/answers/${props.answer.id}`)">DELETE</button>

  </div>
</template>
