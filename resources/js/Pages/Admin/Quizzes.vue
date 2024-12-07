<script setup lang="ts">
import { computed, provide, type Ref, ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import Expand from '@/components/Common/Expand.vue'
import QuizComponent from '@/components/QuizzesPanel/QuizComponent.vue'
import ArchiveDynamicIcon from '@/components/Icons/ArchiveDynamicIcon.vue'
import useCurrentTime from '@/Helpers/CurrentTime'
import CrudPage from '@/components/Crud/CrudPage.vue'
import FormButton from '@/components/Common/FormButton.vue'
import { PlusCircleIcon } from '@heroicons/vue/20/solid'
import { useParams } from '@/Helpers/Params'
import NoContent from '@/components/Common/NoContent.vue'
import { type PageProps } from '@/Types/PageProps'

provide<Ref<number>>('currentTime', useCurrentTime())

defineProps<{ quizzes: Pagination<Quiz> } & PageProps>()

const sortOptions: SortOption[] = [
  { text: 'Po id (rosnąco)', key: 'id' },
  { text: 'Po id (malejąco)', key: 'id', desc: true },
  { text: 'Po nazwie (A–Z)', key: 'title' },
  { text: 'Po nazwie (Z–A)', key: 'title', desc: true },
  { text: 'Od najnowszych', key: 'created_at', desc: true },
  { text: 'Od najstarszych', key: 'created_at' },
  { text: 'Po dacie modyfikacji (rosnąco)', key: 'updated_at' },
  { text: 'Po dacie modyfikacji (malejąco)', key: 'updated_at', desc: true },
]

const params = useParams()
const hideArchivedQuizzes = ref<boolean>(params.archived !== 'true')

const customQueries = computed(() => ({ archived: hideArchivedQuizzes.value }))
</script>

<template>
  <Head title="Testy - Panel administracyjny" />

  <CrudPage
    :options="sortOptions"
    :items="quizzes"
    :custom-queries="customQueries"
    resource-name="quizzes"
    new-button-text="Dodaj test"
    :new-item-data="{ title: 'Nowy test' }"
  >
    <template #actions>
      <button
        :title="`${hideArchivedQuizzes ? 'Wyświetl' : 'Schowaj'} zarchiwizowane testy`"
        class="flex gap-2 hover:bg-primary/5 hover:text-primary duration-200 p-2 rounded-lg h-fit"
        @click="hideArchivedQuizzes = !hideArchivedQuizzes"
      >
        <ArchiveDynamicIcon :active="hideArchivedQuizzes" />

        <span class="hidden sm:block">
          {{ hideArchivedQuizzes ? 'Wyświetl' : 'Schowaj' }} zarchiwizowane testy
        </span>
      </button>

      <Expand class="hidden md:block" />

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
      <QuizComponent :quiz="item" />
    </template>

    <template #noContent="{search}">
      <NoContent :description="search ? `Wygląda na to, że nie mamy tego, czego szukasz.` : undefined">
        <div v-if="!search">
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
      </NoContent>
    </template>
  </CrudPage>
</template>
