<script setup lang="ts">
import { onMounted, provide, type Ref, ref, watch } from 'vue'
import { Head } from '@inertiajs/vue3'
import dayjs from 'dayjs'
import { vAutoAnimate } from '@formkit/auto-animate'
import { ArrowsUpDownIcon } from '@heroicons/vue/24/outline'
import { PlusCircleIcon } from '@heroicons/vue/20/solid'
import Expand from '@/components/Common/Expand.vue'
import FormButton from '@/components/Common/FormButton.vue'
import QuizComponent from '@/components/QuizzesPanel/QuizComponent.vue'
import Dropdown from '@/components/Common/Dropdown.vue'
import ArchiveDynamicIcon from '@/components/Icons/ArchiveDynamicIcon.vue'
import useCurrentTime from '@/Helpers/CurrentTime'
import { keysWrapper } from '@/Helpers/KeysManager'
import type Quiz from '@/Types/Quiz'

provide<Ref<number>>('currentTime', useCurrentTime())
const props = defineProps<{ quizzes:Quiz[] }>()
const quizzes = ref<Quiz[]>(props.quizzes)
const sorter = ref<(a:Quiz, b:Quiz) => number>()
const showArchivedQuizzes = ref<boolean>(true)
const options = keysWrapper([
  { text: 'Po nazwie (A–Z)', action: () => setQuizzesSorter('name') },
  { text: 'Po nazwie (Z–A)', action: () => setQuizzesSorter('name', true) },
  { text: 'Od najnowszych' , action: () => setQuizzesSorter('creationDate', true) },
  { text: 'Od najstarszych', action: () => setQuizzesSorter('creationDate') },
])

onMounted(() => {
  const savedSorter = localStorage.getItem('quizzesSorterPreference')
  const [type, desc] = savedSorter ? JSON.parse(savedSorter) : ['creationDate', true]
  setQuizzesSorter(type, desc)
})

watch(
  [() => props.quizzes, sorter],
  ([newQuizzes, sorter]) => {
    quizzes.value = newQuizzes
    quizzes.value.sort(sorter)
  },
  { immediate: true },
)

function setQuizzesSorter(type: 'name' | 'creationDate', desc = false) {
  localStorage.setItem('quizzesSorterPreference', JSON.stringify([type, desc]))
  sorter.value = (a:Quiz, b:Quiz) => {
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

      <FormButton
        class="rounded-xl"
        button-class="pl-3 font-bold"
        method="post"
        href="/admin/quizzes"
        :data="{ title: 'Nowy test' }"
      >
        <PlusCircleIcon class="size-6 text-white" /> Dodaj test
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
