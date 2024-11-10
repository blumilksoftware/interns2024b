<script setup lang="ts">
import { provide, type Ref, ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import Expand from '@/components/Common/Expand.vue'
import QuizComponent from '@/components/QuizzesPanel/QuizComponent.vue'
import ArchiveDynamicIcon from '@/components/Icons/ArchiveDynamicIcon.vue'
import useCurrentTime from '@/Helpers/CurrentTime'
import CrudPage from '@/components/Crud/CrudPage.vue'
import FormButton from '@/components/Common/FormButton.vue'
import { PlusCircleIcon } from '@heroicons/vue/20/solid'

provide<Ref<number>>('currentTime', useCurrentTime())

defineProps<{ quizzes: Quiz[] }>()

const sortOptions: SortOptionConstructor[] = [
  { text: 'Po nazwie (A–Z)', type: 'title' },
  { text: 'Po nazwie (Z–A)', type: 'title', desc: true },
  { text: 'Od najnowszych' , type: 'creationDate' },
  { text: 'Od najstarszych', type: 'creationDate', desc: true },
  { text: 'Od najnowszych zmienionych', type: 'modificationDate' },
  { text: 'Od najstarszych zmienionych', type: 'modificationDate', desc: true },
]

const showArchivedQuizzes = ref<boolean>(true)
</script>

<template>
  <Head>
    <title>Testy - Panel administracyjny</title>
  </Head>

  <CrudPage
    :options="sortOptions"
    :items="quizzes"
    resource-name="quizzes"
    new-button-text="Dodaj test"
    :new-item-data="{ title: 'Nowy test' }"
  >
    <template #actions>
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
    </template>

    <template #item="{item}">
      <QuizComponent
        :quiz="item"
        :show-archived-quizzes="showArchivedQuizzes"
      />
    </template>
  </CrudPage>
</template>
