<script setup lang="ts">
import {type User} from '@/Types/User'


const props = defineProps<{
  user: User
}>()

</script>

<template>
  <div class="ml-8 border-2">
    <div>id: {{ props.user.id }}</div>
    <div>Name: {{ props.user.name }}</div>
    <div>Surname: {{ props.user.surname }}</div>
    <div>Email: {{ props.user.email }}</div>
    <div>School: {{ props.user.school.name }}</div>
  </div>
</template>

