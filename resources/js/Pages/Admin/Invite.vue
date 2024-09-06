<script setup lang="ts">
import { defineProps } from 'vue'
import type { User } from '@/Types/User'

const props = defineProps<{ users: User[] }>()
</script>

<template>
  Super [działająca] wyszukiwarka użytkowników do testów

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
        <tr v-for="user in props.users" :key="user.id">
          <td>{{ user.id }}</td>
          <td>{{ user.name }}</td>
          <td>{{ user.surname }}</td>
          <td>{{ user.school.name }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
