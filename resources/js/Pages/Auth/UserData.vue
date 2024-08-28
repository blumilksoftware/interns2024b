<script setup lang="ts">
const props = defineProps<{
  user: {
    name: string
    surname: string
    email: string
    school: {
      name: string
      }
    }
  errors: Record<string, string[]>
  status?: string
  }>()
</script>

<template>
  <div class="flex flex-col w-full sm:grid sm:grid-cols-2 gap-4">
    <div class="w-full border border-primary/30 py-2 px-4 rounded-lg bg-white/30">
      <b>Imię:</b> 
      <p>{{ props.user.name }}</p>
    </div>
    <div class="w-full border border-primary/30 py-2 px-4 rounded-lg bg-white/30">
      <b>Nazwisko:</b> 
      <p>{{ props.user.surname }}</p>
    </div>
    <div class="w-full border border-primary/30 py-2 px-4 rounded-lg bg-white/30">
      <b>E-mail:</b> 
      <p>{{ props.user.email }}</p>
    </div>
    <div class="w-full border border-primary/30 py-2 px-4 rounded-lg bg-white/30">
      <b>Szkoła:</b> 
      <p>{{ props.user.school.name }}</p>
    </div>
  </div>
</template>
