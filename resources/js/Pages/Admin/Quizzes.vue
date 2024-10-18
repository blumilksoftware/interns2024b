<script setup lang="ts">
import { provide, type Ref, ref, watch } from 'vue'
import { Head } from '@inertiajs/vue3'
import dayjs from 'dayjs'
import { ArrowsUpDownIcon } from '@heroicons/vue/24/outline'
import { PlusCircleIcon } from '@heroicons/vue/20/solid'
import Expand from '@/components/Common/Expand.vue'
import RequestButton from '@/components/Common/RequestButton.vue'
import QuizComponent from '@/components/QuizzesPanel/QuizComponent.vue'
import Dropdown from '@/components/Common/Dropdown.vue'
import ArchiveDynamicIcon from '@/components/Icons/ArchiveDynamicIcon.vue'
import useCurrentTime from '@/Helpers/CurrentTime'
import { keysWrapper } from '@/Helpers/KeysManager'
import type Quiz from '@/Types/Quiz'

provide<Ref<number>>('currentTime', useCurrentTime())
const props = defineProps<{ quizzes:Quiz[] }>()
const quizzes = ref<Quiz[]>(props.quizzes)
const sorter = ref(getQuizzesSorter('creationDate', true))
const showArchivedQuizzes = ref<boolean>(true)
const options = keysWrapper([
  { text: 'Po nazwie (A–Z)', action: () => sorter.value = getQuizzesSorter('name') },
  { text: 'Po nazwie (Z–A)', action: () => sorter.value = getQuizzesSorter('name', true) },
  { text: 'Od najnowszych' , action: () => sorter.value = getQuizzesSorter('creationDate', true) },
  { text: 'Od najstarszych', action: () => sorter.value = getQuizzesSorter('creationDate') },
])

watch(
  [() => props.quizzes, sorter],
  ([_quizzes, sorter]) => {
    quizzes.value = _quizzes
    quizzes.value.sort(sorter)
  },
  { immediate: true },
)

function getQuizzesSorter(type: 'name' | 'creationDate', desc = false) {
  return (a:Quiz, b:Quiz) => {
    if (desc) [a, b] = [b, a]
    return {
      name: a.title.localeCompare(b.title),
      creationDate: dayjs(a.createdAt).diff(dayjs(b.createdAt)),
    }[type]
  }
}
</script>

<template>
  <Head>
    <title>Testy - Panel administracyjny</title>
  </Head>
  <div class="flex flex-col w-full pb-3">
    <div data-name="toolbar" class="flex px-4 gap-1 sm:gap-2">
      <Dropdown class-btn="rounded-lg" :options="options" title="Sortuj">
        <div class="flex gap-2 hover:bg-primary/5 hover:text-primary duration-200 p-2 rounded-lg">
          <ArrowsUpDownIcon class="size-6" />
          <span class="hidden sm:block">Sortuj</span>
        </div>
      </Dropdown>

      <button
        :title="`${showArchivedQuizzes ? 'Wyświetl' : 'Schowaj'} zarchiwizowane testy`"
        class="flex gap-2 hover:bg-primary/5 hover:text-primary duration-200 p-2 rounded-lg"
        @click="showArchivedQuizzes = !showArchivedQuizzes"
      >
        <ArchiveDynamicIcon :active="showArchivedQuizzes" />
        <span class="hidden sm:block">{{ showArchivedQuizzes ? 'Wyświetl' : 'Schowaj' }} zarchiwizowane testy</span>
      </button>

      <Expand />

      <RequestButton
        class="flex gap-2 items-center bg-primary font-bold rounded-xl text-white pl-3 pr-4 disabled:opacity-50 hover:bg-primary-950 duration-200 transition-colors"
        method="post"
        href="/admin/quizzes"
        :data="{ title: 'Nowy test' }"
      >
        <PlusCircleIcon class="size-6 text-white" /> Dodaj test
      </RequestButton>
    </div>

    <div class="flex flex-col gap-4 p-4">
      <QuizComponent
        v-for="quiz of quizzes"
        :key="quiz.id"
        :quiz="quiz"
        :show-archived-quizzes="showArchivedQuizzes"
      />
    </div>
  </div>
</template>
