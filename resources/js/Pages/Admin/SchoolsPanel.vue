<script setup lang="ts">
import {Head} from '@inertiajs/vue3'
import { PlusCircleIcon } from '@heroicons/vue/20/solid'
import { ArrowsUpDownIcon } from '@heroicons/vue/24/outline'
import { vAutoAnimate } from '@formkit/auto-animate'
import FormButton from '@/components/Common/FormButton.vue'
import Expand from '@/components/Common/Expand.vue'
import Dropdown from '@/components/Common/Dropdown.vue'
</script>

<template>
  <Head>
    <title>Szkoły - Panel administracyjny</title>
  </Head>
  <div class="flex flex-col w-full pb-3">
    <div data-name="toolbar" class="flex px-4 gap-1 sm:gap-2">
      <Dropdown class-btn="rounded-lg" :options="options" title="Sortuj">
        <div class="flex gap-2 hover:bg-primary/5 hover:text-primary duration-200 p-2 rounded-lg">
          <ArrowsUpDownIcon class="size-6" />
          <span class="hidden sm:block">Sortuj</span>
        </div>
      </Dropdown>

      <Expand />

      <FormButton
        class="rounded-xl"
        button-class="pl-3 font-bold"
        method="post"
        href="/admin/quizzes"
        :data="{ title: 'Nowy test' }"
      >
        <PlusCircleIcon class="size-6 text-white" /> Importuj
      </FormButton>

      <FormButton
        class="rounded-xl"
        button-class="pl-3 font-bold"
        method="post"
        href="/admin/quizzes"
        :data="{ title: 'Nowy test' }"
      >
        <PlusCircleIcon class="size-6 text-white" /> Dodaj szkołe
      </FormButton>
    </div>

    <div v-auto-animate class="flex flex-col gap-4 p-4">
      <QuizComponent
        v-for="quiz of quizzes"
        :key="quiz.id"
        :quiz="quiz"
        :show-archived-quizzes="showArchivedQuizzes"
      />
    </div>
  </div>
</template>
