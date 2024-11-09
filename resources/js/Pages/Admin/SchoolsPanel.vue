<script setup lang="ts">
import {Head} from '@inertiajs/vue3'
import { PlusCircleIcon } from '@heroicons/vue/20/solid'
import AddressInput from '@/components/Common/AddressInput.vue'
import CrudPage from '@/components/Common/CrudPage.vue'
import Expand from '@/components/Common/Expand.vue'
import InputWrapper from "@/components/QuizzesPanel/InputWrapper.vue";
import {router} from '@inertiajs/vue3'
import {ref, watch} from 'vue'
import axios from "axios";
import ButtonFrame from "@/components/Common/ButtonFrame.vue";
import vDynamicInputWidth from "@/Helpers/vDynamicInputWidth";

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
  status.value = false;
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
    new-button-text="Dodaj szkołe"
    :new-item-data="{ name: 'Nowa Szkoła', city: 'Legnica', regon: '0', street: '', building_number: '', zip_code: '' }"
    deletable
  >
    <template #actions>
      <Expand />

      <ButtonFrame
        :disabled="!status"
        class="rounded-xl"
        :class="status ? 'cursor-pointer' : 'cursor-text'"
        @click="startFetching()"
      >
        <PlusCircleIcon class="size-6 text-white" /> Importuj
      </ButtonFrame>
    </template>

    <template #deleteMessage="{item}">
      <b class="text-[1.1rem] text-gray-900">Czy na pewno chcesz usunąć "{{ item.name }}"?</b>
      <p class="text-gray-500">Szkoła zostanie usunięta bezpowrotnie.</p>
    </template>

    <template #itemData="data">
      <div class="flex flex-col duration-200 min-h-6.5 gap-2" :class="{'text-sm text-gray-600': !data.editing}">
        <p>Liczba zarejestrowanych uczniów: <b>{{ data.item.numberOfStudents }}</b></p>

        <InputWrapper
          label="Regon:"
          :has-content="!!data.item.regon || data.editing"
          :error="data.errors.item"
          :show-error="data.editing"
        >
          <input
            v-model="data.item.regon"
            v-dynamic-input-width
            type="text"
            name="buildingNumber"
            autocomplete="off"
            :disabled="!data.editing"
            class="text-md transition-none h-fit w-full outline-none font-bold border-b border-transparent bg-transparent focus:border-b-primary"
            :class="{
              'border-b-primary/30 hover:border-b-primary/60 text-primary text-center' : data.editing,
              'border-b-red' : data.errors.item
            }"
          >
        </InputWrapper>

        <AddressInput v-model="data.item" :errors="data.errors" :disabled="!data.editing" />
      </div>
    </template>
  </CrudPage>
</template>
