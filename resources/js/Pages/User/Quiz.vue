<script setup lang="ts">

  import {QuizSubmission} from "@/Types/QuizSubmission";
  import Divider from "@/components/Common/Divider.vue";
  import {computed, ref} from "vue";
  import axios from "axios";
  import FormButton from "@/components/Common/FormButton.vue";
  import Button from "@/components/Common/Button.vue";
  import {AnswerRecord} from "@/Types/AnswerRecord";
  import {Dialog, DialogPanel, DialogTitle} from "@headlessui/vue";

  const props = defineProps<{ submission: QuizSubmission }>()
  const answers = computed((() => props.submission.answers.sort((a, b) => a.id - b.id)));
  const allAnswered = computed((() => answers.value.every(answer => answer.selected != null)));

  const openDialog = ref(true);

  function handleAnswer(answers: AnswerRecord, selected: number) {
    axios.post(`/answers/${answers.id}/${selected}`, { _method: 'patch' });
    answers.selected = selected;
  }
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

      <div class="mb-3 mt-2">
        <form class="flex flex-col gap-2">
          <label v-for="answer in record.answers" :key="answer.id" class="flex items-center text-sm text-black">
            <input
              type="radio"
              :id="answer.id"
              :checked="record.selected == answer.id"
              class="mr-2 h-6 w-6 border-black text-primary accent-primary"
              @change.prevent="handleAnswer(record, answer.id)" />
            {{ answer.text }}
          </label>
        </form>
      </div>
    </div>

    <div class="h-80 flex flex-col items-center justify-center">
      <p class="font-semibold text-primary text-xl p-5">To już wszystkie pytania. Czy chcesz oddać test?</p>
      <FormButton v-if="allAnswered" small href="/submissions/{quizSubmission}/close" method="post">Oddaj test</FormButton>
      <Button v-else small>Oddaj test</Button>
    </div>
  </div>

  <Dialog :open="openDialog" @close="openDialog = false" class="relative z-50">
    <div class="fixed inset-0 bg-black/30" aria-hidden="true" />

    <div class="fixed inset-0 flex w-screen items-center justify-center p-4">
      <DialogPanel class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
          <h3 class="text-base font-semibold leading-6 text-xl text-primary">Uwaga</h3>
          <div class="mt-2 max-w-xl text-black">
            <p>Nie udzielono odpowiedzi na wszystkie pytania, czy na pewno chcesz oddać test?</p>
          </div>
          <div class="mt-5 flex gap-4">
            <Button small  @click="openDialog = false">Wróć</Button>
            <FormButton small href="/submissions/{quizSubmission}/close" method="post">Oddaj mimo to</FormButton>
          </div>
        </div>
      </DialogPanel>
    </div>
  </Dialog>
</template>
