<script setup lang="ts">
import {Head} from '@inertiajs/vue3'
import {type Quiz} from '@/Types/Quiz'
import LinkButton from '@/components/Common/LinkButton.vue'

defineProps<{ quizzes: Quiz[] }>()

</script>

<template>
  <Head>
    <title>Testy</title>
  </Head>
  Quizzes - CRUD

  <div class="w-4/5">
    <div v-for="quiz in quizzes" :key="quiz.id" class="m-4 bg-white w-100 rounded-2xl p-4 border shadow flex gap-4 items-center justify-between">
      <b>{{ quiz.name }}</b>
      <LinkButton :href="`/admin/quizzes/${quiz.id}/ranking`" small>Ranking</LinkButton>
    </div>
  </div>
</template>
