<script setup lang="ts">
import {Head, router} from '@inertiajs/vue3'
import { ArrowUpCircleIcon } from '@heroicons/vue/20/solid'
import AddressInput from '@/components/Common/AddressInput.vue'
import CrudPage from '@/components/Crud/CrudPage.vue'
import Expand from '@/components/Common/Expand.vue'
import InputWrapper from '@/components/QuizzesPanel/InputWrapper.vue'
import {ref, watch} from 'vue'
import axios from 'axios'
import Button from '@/components/Common/Button.vue'
import vDynamicTextAreaHeight from '@/Helpers/vDynamicTextAreaHeight'
import CrudInput from '@/components/Crud/CrudInput.vue'

defineProps<{schools: Pagination<School>}>()

const sortOptions: SortOption[] = [
  { text: 'Po id (rosnąco)', key: 'id' },
  { text: 'Po id (malejąco)', key: 'id', desc: true },
  { text: 'Po nazwie (rosnąco)', key: 'name' },
  { text: 'Po nazwie (malejąco)', key: 'name', desc: true },
  { text: 'Po liczbie uczniów (rosnąco)', key: 'students' },
  { text: 'Po liczbie uczniów (malejąco)', key: 'students', desc: true },
  { text: 'Po REGON (rosnąco)', key: 'regon' },
  { text: 'Po REGON (malejąco)', key: 'regon', desc: true },
  { text: 'Po adresie (rosnąco)', key: 'address' },
  { text: 'Po adresie (malejąco)', key: 'address', desc: true },
  { text: 'Od najnowszych' , key: 'created_at', desc: true },
  { text: 'Od najstarszych', key: 'created_at' },
  { text: 'Od najpóźniej zmienionych', key: 'updated_at' },
  { text: 'Od najwcześniej zmienionych', key: 'updated_at', desc: true },
]

const status = ref<boolean | null>(null)

async function isImporting(): Promise<boolean> {
  return await axios.get('/admin/schools/status')
    .then(res => !res.data.done)
    .catch(() => true)
}

setInterval(async () => {
  if (!status.value) {
    status.value = !(await isImporting())
  }
}, 1000)

watch(status, () => router.reload())

function startFetching() {
  if (status.value) {
    status.value = false
    axios.post('/admin/schools/fetch')
  }
}
</script>

<template>
  <Head>
    <title>Szkoły - Panel administracyjny</title>
  </Head>

  <Transition>
    <div v-show="status === false" class="fixed bg-white/50 backdrop-blur-md z-10 size-full left-0 top-0 flex items-center justify-center gap-2">
      <div class="inline-block size-8 animate-spin rounded-full border-4 border-solid border-current border-e-transparent align-[-0.125em] text-primary motion-reduce:animate-[spin_1.5s_linear_infinite]" role="status" />
      <p>Trwa importowanie szkół.</p>
    </div>
  </Transition>

  <CrudPage
    :options="sortOptions"
    :items="schools"
    :custom-search="(text) => text?.toLocaleUpperCase()"
    resource-name="schools"
    new-button-text="Dodaj szkołę"
    :new-item-data="{ name: 'Nowa szkoła', regon: '', apartmentNumber: '', street: '', buildingNumber: '', city: '', numberOfStudents: 0, zipCode: '' }"
    display-search-in-lower-case
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
        :hide-content="!item.name && !editing"
      >
        <textarea
          v-model="item.name"
          v-dynamic-text-area-height
          name="name"
          autocomplete="off"
          :disabled="!editing"
          placeholder=""
          class="size-full xs:h-8 bg-transparent outline-none font-bold text-lg xs:mb-0"
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
          v-model="data.item.regon"
          name="regon"
          label="REGON:"
          :editing="data.editing"
          :error="data.errors.regon"
        />

        <AddressInput v-model="data.item" :errors="data.errors" :disabled="!data.editing" />
      </div>
    </template>
  </CrudPage>
</template>
