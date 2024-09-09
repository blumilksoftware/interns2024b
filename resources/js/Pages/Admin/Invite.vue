<script setup lang="ts">
import {defineProps} from 'vue'
import { type  User } from '@/Types/User'
import { type Pagination} from '@/Types/Pagination'
import FormButton from '@/components/Common/FormButton.vue'
import { type Quiz} from '@/Types/Quiz'

defineProps<{
  users: Pagination<User>
  quiz: Quiz
}>()
</script>

<template>
  <h1>Quiz: {{ quiz.name }}</h1>
  Wszukiwarka użytkowników do zapraszania do testów

  <div>
    <table>
      <thead>
        <tr>
          <th>ID Użytkownika</th>
          <th>Imię</th>
          <th>Nazwisko</th>
          <th>Szkoła</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="user in users.data" :key="user.id">
          <td>{{ user.id }}</td>
          <td>{{ user.name }}</td>
          <td>{{ user.surname }}</td>
          <td>{{ user.school.name }}</td>
        </tr>
      </tbody>
    </table>

    <FormButton :disabled="!users.links.prev" method="get" :href="users.links.prev" small>Poprzednia</FormButton>
    <FormButton :disabled="!users.links.next" method="get" :href="users.links.next" small>Następna</FormButton>
  </div>
</template>
