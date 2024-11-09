<script setup lang="ts">
import {Head} from '@inertiajs/vue3'
import { PlusCircleIcon } from '@heroicons/vue/20/solid'
import FormButton from '@/components/Common/FormButton.vue'
import AddressInput from '@/components/Common/AddressInput.vue'
import CrudPage from '@/components/Common/CrudPage.vue'
import Expand from '@/components/Common/Expand.vue'

defineProps<{schools: School[]}>()

const sortOptions: SortOptionConstructor[] = [
  { text: 'Po nazwie (A–Z)', type: 'name' },
  { text: 'Po nazwie (Z–A)', type: 'name', desc: true },
  { text: 'Od najnowszych' , type: 'creationDate' },
  { text: 'Od najstarszych', type: 'creationDate', desc: true },
  { text: 'Od najnowszych zmienionych', type: 'modificationDate' },
  { text: 'Od najstarszych zmienionych', type: 'modificationDate', desc: true },
]
</script>

<template>
  <Head>
    <title>Szkoły - Panel administracyjny</title>
  </Head>

  <CrudPage
    :options="sortOptions"
    :items="schools"
    resource-name="schools"
    new-button-text="Dodaj szkołe"
    :new-item-data="{ }"
    deletable
  >
    <template #actions>
      <Expand />

      <FormButton
        class="rounded-xl"
        button-class="pl-3 font-bold"
        method="post"
        href="/admin/quizzes"
        :data="{ title: 'Nowy test' }"
      >
        <PlusCircleIcon class="size-6 text-white" /> Importuj
      </FormButton>
    </template>

    <template #deleteMessage="{item}">
      <b class="text-[1.1rem] text-gray-900">Czy na pewno chcesz usunąć "{{ item.name }}"?</b>
      <p class="text-gray-500">Szkoła zostanie usunięta bezpowrotnie.</p>
    </template>

    <template #itemData="data">
      <div class="flex flex-col duration-200 min-h-6.5 gap-2" :class="{'text-sm text-gray-600': !data.editing}">
        <p>Liczba zarejestrowanych uczniów: <b>{{ data.item.numberOfStudents }}</b></p>

        <AddressInput v-model="data.item" :errors="data.errors" :disabled="!data.editing" />
      </div>
    </template>
  </CrudPage>
</template>
