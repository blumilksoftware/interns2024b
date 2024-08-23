<script setup lang="ts">
import AdminPanelLayout from '@/Layouts/AdminPanelLayout.vue'
import { ref } from 'vue'
import SortIcon from '@/components/Icons/SortIcon.vue'
import EyeDynamicIcon from '@/components/Icons/EyeDynamicIcon.vue'
import FilterIcon from '@/components/Icons/FilterIcon.vue'
import QuizComponent from '@/components/QuizzesPanel/QuizComponent.vue'
import { type Quiz } from '@/Types/Quiz'
const props = defineProps<{ quizzes: Quiz[] }>()
const selectedQuiz = ref<number>()
const showLockedQuizzes = ref<boolean>(true)

function addQuiz() {
  // router.post(`/quizzes/${quiz.id}`)
}

function toggleQuizView(quiz: Quiz) {
  selectedQuiz.value = selectedQuiz.value === quiz.id ? undefined : quiz.id
}

// public function testUserCanCreateQuiz(): void
// {
//     $this->actingAs($this->user)
//         ->from("/")
//         input "name" field in form
//         ->post("/quizzes", ["name" => "Example quiz"])
//         ->assertRedirect("/");

//     $this->assertDatabaseHas("quizzes", [
//         "name" => "Example quiz",
//     ]);
// }
</script>

<template>
  <AdminPanelLayout>
    <template #subheader>
      <button>Filtruj</button>
      <button>Sortuj</button>
      <div class="w-full" />
      <button @click="addQuiz">+&nbsp;Dodaj&nbsp;test</button>
    </template>
    <template #items />
    <template #selectedItemContent />
  </AdminPanelLayout>

  <div class="flex flex-col w-full">
    <div data-name="toolbar" class="flex gap-5 bg-white/70 px-4 py-2 backdrop-blur-md">
      <button class="flex gap-2 hover:bg-primary/5 duration-200 p-2 rounded-lg">
        <FilterIcon />
        Filtruj
      </button>
      <button class="flex gap-2 hover:bg-primary/5 duration-200 p-2 rounded-lg">
        <SortIcon />
        Sortuj
      </button>
      <button class="flex gap-2 hover:bg-primary/5 duration-200 p-2 rounded-lg" @click="showLockedQuizzes=!showLockedQuizzes">
        <EyeDynamicIcon :is-opened="showLockedQuizzes" />
        {{ showLockedQuizzes ? 'Pokaż' : 'Schowaj' }} zablokowane
      </button>
    </div>
    <div v-for="quiz in props.quizzes" :key="quiz.id" class="px-4">
      <QuizComponent
        :quiz="quiz"
        :is-selected="selectedQuiz===quiz.id"
        :show-locked-quizzes="showLockedQuizzes"
        @display-toggle="toggleQuizView"
      />
    </div>
  </div>
</template>
