<script setup lang="ts">
import CustomRadio from '@/components/Common/CustomRadio.vue'

defineProps<{ index: number, question: UserQuestion, questionsTotal: number, timeout: boolean }>()
const emit = defineEmits<{ answer: [question: UserQuestion, answerId: number]}>()
</script>

<template>
  <div class="rounded-xl bg-white shadow border flex flex-col justify-between gap-5 p-5 pb-6">
    <div class="flex flex-col gap-3">
      <b class="text-primary text-lg">Pytanie: {{ index + 1 }}/{{ questionsTotal }}</b>
      <p class="font-medium">{{ question.text }}</p>
    </div>

    <form class="flex flex-col gap-5">
      <label
        v-for="answer in question.answers" :key="answer.id"
        class="flex gap-2.5 text-gray-900"
        :class="timeout ? 'cursor-not-allowed' : 'cursor-pointer'"
        :title="timeout ? 'Czas przewidziany na ten test dobiegł końca' : undefined"
      >
        <CustomRadio
          class="mt-[.3rem] text-primary"
          :class="timeout ? 'cursor-not-allowed' : 'cursor-pointer'"
          :size="1"
          :disabled="timeout"
          :checked="question.selectedAnswer === answer.id"
          @change="emit('answer', question, answer.id)"
        />
        {{ answer.text }}
      </label>
    </form>
  </div>
</template>
