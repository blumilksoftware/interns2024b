<script setup lang="ts">
import {Head} from '@inertiajs/vue3'
import { ArrowUpCircleIcon } from '@heroicons/vue/20/solid'
import AddressInput from '@/components/Common/AddressInput.vue'
import CrudPage from '@/components/Crud/CrudPage.vue'
import Expand from '@/components/Common/Expand.vue'
import InputWrapper from '@/components/QuizzesPanel/InputWrapper.vue'
import {router} from '@inertiajs/vue3'
import {ref, watch} from 'vue'
import axios from 'axios'
import Button from '@/components/Common/Button.vue'
import vDynamicTextAreaHeight from '@/Helpers/vDynamicTextAreaHeight'
import CrudInput from '@/components/Crud/CrudInput.vue'

defineProps<{schools: School[]}>()

const sortOptions: SortOptionConstructor[] = [
  { text: 'Po nazwie (A–Z)', type: 'name' },
  { text: 'Po nazwie (Z–A)', type: 'name', desc: true },
  { text: 'Od najnowszych' , type: 'creationDate' },
  { text: 'Od najstarszych', type: 'creationDate', desc: true },
  { text: 'Od najnowszych zmienionych', type: 'modificationDate' },
  { text: 'Od najstarszych zmienionych', type: 'modificationDate', desc: true },
]

const status = ref(false)

setInterval(() => {
  axios.get('/admin/schools/status')
    .then(res => status.value = res.data.done)
    .catch(() => status.value = false)
}, 1000)

watch(status, () => router.reload())

function startFetching() {
  status.value = false
  axios.post('/admin/schools/fetch')
}
</script>

<template>
  <Head>
    <title>Szkoły - Panel administracyjny</title>
  </Head>

  <CrudPage
    :options="sortOptions"
    :items="schools"
    resource-name="schools"
    new-button-text="Dodaj szkołę"
    :new-item-data="{ name: '', regon: '', apartmentNumber: '', street: '', buildingNumber: '', city: '', numberOfStudents: 0, zipCode: '' }"
    deletable
    mobile-nav
    creatable
  >
    <template #actions>
      <Expand />

      <Button
        :disabled="!status"
        class="rounded-xl"
        :class="{'cursor-pointer': status}"
        @click="startFetching()"
      >
        <ArrowUpCircleIcon class="size-6 text-white" /> Importuj szkoły
      </Button>
    </template>

    <template #deleteMessage="{item}">
      <b class="text-[1.1rem] text-gray-900">Czy na pewno chcesz usunąć "{{ item.name }}"?</b>
      <p class="text-gray-500">Szkoła zostanie usunięta bezpowrotnie.</p>
    </template>

    <template #title="{item, editing, errors}">
      <InputWrapper
        :error="errors.name"
        :hide-error="!editing"
      >
        <textarea
          v-model="item.name"
          v-dynamic-text-area-height
          name="name"
          autocomplete="off"
          :disabled="!editing"
          placeholder=""
          class="size-full xs:h-8 bg-transparent outline-none font-bold text-lg xs:mb-0 resize-none"
          :class="{
            'border-b border-b-primary/30 duration-200 transition-colors hover:border-b-primary/60 text-primary' : editing,
            'border-b-red' : errors.name
          }"
        />
      </InputWrapper>
    </template>

    <template #itemData="data">
      <div class="flex flex-col duration-200 min-h-6.5 gap-2" :class="{'text-sm text-gray-600': !data.editing}">
        <p>Liczba uczniów: <b>{{ data.item.numberOfStudents }}</b></p>

        <CrudInput
          name="regon"
          label="Regon:"
          :editing="data.editing"
          :error="data.errors.regon"
          :model-value="data.item.regon"
        />

        <AddressInput v-model="data.item" :errors="data.errors" :disabled="!data.editing" />
      </div>
    </template>
  </CrudPage>
</template>
