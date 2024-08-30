<script setup lang="ts">

import {type User} from '@/Types/User'
import Show from './Show.vue'

const props = defineProps<{
  users: User[]
  isSuperAdmin: boolean
}>()

</script>

<template>
  <h1 v-if="props.users.length > 0" class="text-xl">UŻYTKOWNICY</h1>
  <div v-for="item in props.users" :key="item.id">
    <Show :user="item" :is-super-admin="props.isSuperAdmin" />
  </div>
</template>
