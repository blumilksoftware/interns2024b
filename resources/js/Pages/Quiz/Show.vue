<script setup lang="ts">
  import {type Quiz} from "@/Types/Quiz";
  import Questions from "../Question/Index.vue"
  import axios from 'axios'
  import {router} from "@inertiajs/vue3";

  const props = defineProps<{
    quiz: Quiz
  }>()
</script>

<template>
  <div class="ml-8 border-2">
    <div>id: {{props.quiz.id}}</div>
    <div>name: {{props.quiz.name}}</div>
    <div>created: {{props.quiz.createdAt}}</div>
    <div>updated: {{props.quiz.updatedAt}}</div>
    <div>locked: {{props.quiz.locked}}</div>
    <button class="font-bold block" @click="router.post(`/quizzes/${props.quiz.id}/lock`)">LOCK</button>
    <button class="font-bold block" @click="router.delete(`/quizzes/${props.quiz.id}`)">DELETE</button>

    <Questions :questions="props.quiz.questions" />
  </div>
</template>
