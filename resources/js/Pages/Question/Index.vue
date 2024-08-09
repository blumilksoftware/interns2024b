<script setup lang="ts">
import Show from "./Show.vue";
import {type Question} from "@/Types/Question";

const props = defineProps<{
  questions: Question[]
}>()
</script>

<template>
  <h1 v-if="props.questions.length > 0" class="text-xl">QUESTIONS</h1>
  <div v-for="item in props.questions" key="item.id">
    <Show :question="item" />
  </div>
</template>
