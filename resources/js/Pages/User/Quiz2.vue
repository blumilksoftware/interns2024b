<script setup lang="ts">

  import {QuizSubmission} from "@/Types/QuizSubmission";
  import Divider from "@/components/Common/Divider.vue";
  import Answer from "@/components/QuizSubmission/Answer.vue";
  import {computed} from "vue";

  const props = defineProps<{ submission: QuizSubmission }>()
  const answers = computed((() => props.submission.answers.sort((a, b) => a.id - b.id)));
</script>

<template>
  <div class="w-full p-2 md:w-7/12">
    <Divider>
      <h1 class="font-bold text-xl text-primary text-center px-4 whitespace-nowrap">
        {{ submission.name }}
      </h1>
    </Divider>

    <div v-for="(record, index) in answers" :key="record.id" class="rounded-lg bg-white shadow border flex flex-col justify-between px-4 py-2 m-5">
      <div>
        <p class="pt-2 font-semibold text-primary">Pytanie: {{ index + 1 }}/{{ answers.length }}</p>
        <p class="py-2 mt-2">{{record.question}}</p>
      </div>

      <div class="mb-3 mt-auto">
        <p class="text-sm font-semibold text-primary py-4">Zaznacz poprawną odpowiedź</p>
        <div class="grid grid-cols-2 gap-4">
          <Answer
            v-for="answer in record.answers"
            :key="answer.id"
            :record-id="record.id"
            :answer="answer"
            :selected="record.selected === answer.id"
          />
        </div>
      </div>
    </div>
  </div>
</template>
