<!-- eslint-disable @typescript-eslint/no-unused-vars -->
<script setup lang="ts">
import { ref, watch } from 'vue'
import { type Quiz } from '@/Types/Quiz'
import {Request} from '@/scripts/request'
const props = defineProps<{ quizzes: Quiz[] }>()
const quizzesRef = ref<Quiz[]>(addKeys(props.quizzes))
import { type Question } from '@/Types/Question'
import { type Answer } from '@/Types/Answer'
import { type CleanQuiz } from '@/Types/CleanQuiz'
import { type VisitPayload } from '@/Types/VisitPayload'
import dayjs from 'dayjs'
watch(
  () => props.quizzes,
  (updatedQuizzes : Quiz[]) => quizzesRef.value = addKeys(updatedQuizzes),
)

function addKeys(quizzes:Quiz[]) {
  return quizzes.map(
    (quiz: Quiz)=>{
      return {
        ...quiz, key: quiz.key ?? quiz.id, questions: quiz.questions.map(
          (q:Question)=>{
            return {
              ...q, key: q.key ?? q.id, answers: q.answers.map(
                (ans:Answer)=>{
                  return {...ans, key: quiz.key ?? ans.id}
                },
              ),
            }
          },
        ),
      }
    },
  )
}

const request = new Request()

function addExampleQuiz() {
  const data : CleanQuiz= { name: 'Nowy test', key:1, questions:[]}
  request.sendRequest('/admin/quizzes',{data: data, method: 'post'})
}

function lock(quiz:Quiz) {
  request.sendRequest(`/quizzes/${quiz.id}/lock`,{method:'patch'})
}
function unlock(quiz:Quiz) {
  request.sendRequest(`/quizzes/${quiz.id}/unlock`,{method:'patch'})
}
function deleteQuiz(quiz:Quiz) {
  request.sendRequest(`/admin/quizzes/${quiz.id}`, {method: 'delete'})
}
function copyQuiz(quiz:Quiz) {
  request.sendRequest(`/admin/quizzes/${quiz.id}/clone`, {method: 'post'})
}
function updateQuiz(quiz:Quiz) {
  const payload : VisitPayload = {
    method: 'patch',
    data: quiz,
  }
  request.sendRequest(`/admin/quizzes/${quiz.id}`, payload)
}

// date formatters
function formatDatePretty(date?:number | string):string|undefined{
  return date ? dayjs(date).format('MMM D, YYYY - h:mm').toString() : undefined
}
function formatDateStandard(date?:number | string):string|undefined{
  return date ? dayjs(date).format().toString() : undefined
}
</script>

<template>
  <div class="flex flex-col gap-5">
    <header class="flex gap-5">
      <button @click="addExampleQuiz">Add example quiz</button>
    </header>
    <div v-for="quiz of quizzesRef" :key="quiz.key" class="bg-white shadow grid grid-cols-2 gap-5 p-5 rounded-lg">
      <span>id:           </span> <b>{{ quiz.id }}          </b>
      <span>key:          </span> <b>{{ quiz.key }}         </b>
      <span>name:         </span> <b>{{ quiz.name }}        </b>
      <span>scheduledAt:  </span> <b>{{ quiz.scheduledAt }} </b>
      <span>duration:     </span> <b>{{ quiz.duration }}    </b>
      <span>questions:    </span> <b>{{ quiz.questions }}   </b>
      <span>state:        </span> <b>{{ quiz.state }}       </b>
      <span>createdAt:    </span> <b>{{ quiz.createdAt }}   </b>
      <span>updatedAt:    </span> <b>{{ quiz.updatedAt }}   </b>
      <hr><hr>
      <button @click="unlock(quiz)">Unlock</button>
      <button>Copy</button>
      <button @click="lock(quiz)">Lock</button>
      <button class="text-red" @click="deleteQuiz(quiz)">Delete</button>
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
