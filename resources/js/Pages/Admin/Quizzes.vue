<script setup lang="ts">

import { ref, watch } from 'vue'
import {Request} from '@/scripts/request'
import SortIcon from '@/components/Icons/SortIcon.vue'
import EyeDynamicIcon from '@/components/Icons/EyeDynamicIcon.vue'
import QuizComponent from '@/components/QuizzesPanel/QuizComponent.vue'
import { type Quiz } from '@/Types/Quiz'
import { type Question } from '@/Types/Question'
import { type Answer } from '@/Types/Answer'
import Dropdown from '@/components/Common/Dropdown.vue'
import Banner from '@/components/Common/Banner.vue'
import { type CleanQuiz } from '@/Types/CleanQuiz'
import { type VisitPayload } from '@/Types/VisitPayload'
import { type CleanAnswer } from '@/Types/CleanAnswer'
import { type CleanQuestion } from '@/Types/CleanQuestion'

const props = defineProps<{ quizzes: Quiz[] }>()
const quizzesRef = ref<Quiz[]>(addKeys(props.quizzes))

watch(
  () => props.quizzes,
  (updatedQuizzes : Quiz[]) => quizzesRef.value = addKeys(updatedQuizzes),
)


const request = new Request()

function addExampleQuiz() {
  const data : CleanQuiz= { name: 'Nowy test', key:1, questions:[]}
  request.sendRequest('/admin/quizzes',{data: data, method: 'post'})
}

function lock(quiz:Quiz) {
  request.sendRequest(`/admin/quizzes/${quiz.id}/lock`,{method:'post'})
}
function unlock(quiz:Quiz) {
  request.sendRequest(`/admin/quizzes/${quiz.id}/unlock`,{method:'post'})
}
function deleteQuiz(quiz:Quiz) {
  request.sendRequest(`/admin/quizzes/${quiz.id}`, {method: 'delete'})
}
function copyQuiz(quiz:Quiz) {
  request.sendRequest(`/admin/quizzes/${quiz.id}/clone`, {method: 'post'})
}
function updateWithExampleData(quiz:Quiz) {
  const cleanAnswer1: CleanAnswer = {key:9001, text:'answer1', correct: true}
  const cleanAnswer2: CleanAnswer = {key:9002, text:'answer2', correct: false}
  const cleanQuestion: CleanQuestion = {
    key: 8001,
    text: 'question1',
    answers:[
      cleanAnswer1,
      cleanAnswer2,
    ],
  }
  const payload : VisitPayload = {
    method: 'patch',
    data: {
      key : 1,
      name : 'b',
      scheduledAt : '2025-09-05T09:47:32',
      duration : 20,
      questions : [ cleanQuestion ],
    },
  }
  request.sendRequest(`/admin/quizzes/${quiz.id}`, payload)
}

function sortByNameAscending(){
  quizzes.value.sort((a:Quiz, b:Quiz) => a.name.localeCompare(b.name))
}

function sortByNameDescending(){
  quizzes.value.sort((a:Quiz, b:Quiz) => b.name.localeCompare(a.name))
}
</script>

<template>
  <div class="flex flex-col gap-5">
    <header class="flex gap-5">
      <button title="works" @click="addExampleQuiz">Add example quiz</button>
    </header>
    <div v-for="quiz of quizzesRef" :key="quiz.key" class="bg-white shadow grid grid-cols-2 gap-5 p-5 rounded-lg">
      <span>id:           </span> <b>{{quiz.id ?? quiz.key }} </b>
      <span>name:         </span> <b>{{ quiz.name }}        </b>
      <span>scheduledAt:  </span> <b>{{ quiz.scheduledAt }} </b>
      <span>duration:     </span> <b>{{ quiz.duration }}    </b>
      <span>questions:    </span> <b>{{ quiz.questions }}   </b>
      <span>state:        </span> <b>{{ quiz.state }}       </b>
      <span>createdAt:    </span> <b>{{ quiz.createdAt }}   </b>
      <span>updatedAt:    </span> <b>{{ quiz.updatedAt }}   </b>
      <hr><hr>
      <button :disabled="quiz.state === 'unlocked'" class="disabled:text-black/50" @click="unlock(quiz)">Unlock</button>
      <button title="works" @click="copyQuiz(quiz)">Copy</button>
      <button title="works" :disabled="quiz.state === 'locked'" class="disabled:text-black/50" @click="lock(quiz)">Lock</button>
      <button title="works" class="text-red" @click="deleteQuiz(quiz)">Delete</button>
      <button title="Doesn't update scheduledAt. Doesn't let more than one question through" class="text-primary" @click="updateWithExampleData(quiz)">Update with example data</button>
      {{ request.error }}
    </div>
  </div>
</template>

<style scoped>
  button{
    border: 2px solid black;
    border-radius: .5rem;
    padding: .8rem;
    font-weight: bold;
  }
</style>
