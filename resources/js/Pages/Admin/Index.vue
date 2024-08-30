<script setup lang="ts">

import {type User} from '@/Types/User'
import Show from './Show.vue'
import {useForm} from '@inertiajs/vue3'

const form = useForm({})

const props = defineProps<{
  users: User[]
}>()

function addAdmin() {
  form.get('/admin/admins/add')
}
</script>

<template>
  <h1 v-if="props.users.length > 0" class="text-xl">ADMINISTRATORZY</h1>
  <div>
    <form @submit.prevent="addAdmin">
      <button type="submit">Dodaj nowego administratora</button>
    </form>
  </div>
  <div v-for="item in props.users" :key="item.id">
    <Show :user="item" />
  </div>
</template>
