<script setup lang="ts">
import {Head} from '@inertiajs/vue3'
import CrudPage from '@/components/Crud/CrudPage.vue'
import CrudInput from '@/components/Crud/CrudInput.vue'
import vDynamicInputWidth from '@/Helpers/vDynamicInputWidth'
import CrudSchoolInput from '@/components/Crud/CrudSchoolInput.vue'
import WarningMessageBox from '@/components/Common/WarningMessageBox.vue'
import {ref} from 'vue'
import RequestWrapper from '@/components/Common/RequestWrapper.vue'
import {type PageProps} from '@/Types/PageProps'
import { TrashIcon, PencilIcon } from '@heroicons/vue/24/outline'
import ArchiveDynamicIcon from '@/components/Icons/ArchiveDynamicIcon.vue'
import Expand from '@/components/Common/Expand.vue'

defineProps<{ users: Pagination<User>, schools: School[] } & PageProps>()

const showWarning = ref<Record<string, boolean>>({})
const showAnonymizedUsers = ref<boolean>(true)

const sortOptions: SortOption[] = [
  { text: 'Po nazwie (A–Z)', key: 'firstname' },
  { text: 'Po nazwie (Z–A)', key: 'firstname', desc: true },
  { text: 'Po szkole (A-Z)', key: 'school' },
  { text: 'Po szkole (Z-A)', key: 'school',  desc: true },
  { text: 'Od najnowszych' , key: 'created_at' },
  { text: 'Od najstarszych', key: 'created_at', desc: true },
  { text: 'Po dacie modyfikacji (rosnąco)', key: 'updated_at' },
  { text: 'Po dacie modyfikacji (malejąco)', key: 'updated_at', desc: true },
]

function customQueries(): string[] {
  let query: string[] = []

  if (!showAnonymizedUsers.value) {
    query.push(`anonymized=${true}`)
  }

  return query
}
</script>

<template>
  <Head>
    <title>Szkoły - Panel administracyjny</title>
  </Head>

  <CrudPage
    :options="sortOptions"
    :items="users"
    :custom-queries="customQueries"
    resource-name="users"
    disable-edit-button
    mobile-nav
  >
    <template #actions>
      <button
        :title="`${showAnonymizedUsers ? 'Wyświetl' : 'Schowaj'} zanonimizowanych studentów`"
        class="flex gap-2 hover:bg-primary/5 hover:text-primary duration-200 p-2 rounded-lg"
        @click="showAnonymizedUsers = !showAnonymizedUsers"
      >
        <ArchiveDynamicIcon :active="showAnonymizedUsers" />
        <span class="hidden sm:block">{{ showAnonymizedUsers ? 'Wyświetl' : 'Schowaj' }} zanonimizowanych studentów</span>
      </button>

      <Expand />
    </template>

    <template #title="{item, editing, errors: editErrors}">
      <div class="flex gap-1">
        <label v-if="editing" for="name">
          Imie:
        </label>

        <input
          id="firstname"
          v-model="item.firstname"
          v-dynamic-input-width
          type="text"
          name="firstname"
          autocomplete="off"
          class="text-md transition-none h-fit w-full outline-none font-bold border-b border-transparent bg-transparent focus:border-b-primary"
          :class="{
            'border-b-primary/30 hover:border-b-primary/60 text-primary text-center' : editing,
            'border-b-red' : !!editErrors.firstname,
          }"
          :disabled="!editing"
        >

        <label v-if="editing" for="surname">
          Nazwisko:
        </label>

        <input
          v-model="item.surname"
          v-dynamic-input-width
          type="text"
          name="surname"
          autocomplete="off"
          class="text-md transition-none h-fit w-full outline-none font-bold border-b border-transparent bg-transparent focus:border-b-primary"
          :class="{
            'border-b-primary/30 hover:border-b-primary/60 text-primary text-center' : editing,
            'border-b-red' : !!editErrors.surname,
          }"
          :disabled="!editing"
        >
      </div>

      <span v-if="editErrors.name || editErrors.surname" class="text-red text-sm truncate">
        {{ editErrors.name ?? editErrors.surname }}
      </span>
    </template>

    <template #itemActions="{item, editMode}">
      <button v-if="!item.isAnonymized" title="Edytuj" @click="() => editMode(true)">
        <PencilIcon class="icon slide-up-animation" />
      </button>

      <button v-if="user?.isSuperAdmin && !item.isAnonymized" title="Anonimizuj" @click="showWarning[item.id] = true">
        <TrashIcon class="icon slide-up-animation text-red hover:text-red-500" />
      </button>
    </template>

    <template #itemData="{ item, errors, editing }">
      <div class="flex flex-col duration-200 min-h-6.5" :class="{'text-sm text-gray-600': !editing}">
        <CrudInput
          v-model="item.email"
          name="email"
          label="Email:"
          :error="errors.email"
          :editing="editing"
        />

        <CrudSchoolInput
          :value="item.school?.id"
          :schools="schools"
          :errors="errors"
          :editing="editing"
          @change="school => item.school_id = school"
        />
      </div>

      <Teleport to="body">
        <WarningMessageBox :open="!!showWarning[item.id]" @close="showWarning[item.id] = false">
          <template #message>
            <b class="text-[1.1rem] text-gray-900">Czy na pewno chcesz zanonimizować "{{ item.firstname }}"?</b>
            <p class="text-gray-500">Dane zostaną utracone bezpowrotnie.</p>
          </template>

          <template #buttons>
            <RequestWrapper
              class="bg-red font-bold text-white rounded-lg px-4 py-2"
              title="Anonimizuj"
              method="patch"
              :href="`/admin/users/${item.id}/anonymize`"
              @click="showWarning[item.id] = false"
            >
              Anonimizuj
            </RequestWrapper>
          </template>
        </WarningMessageBox>
      </Teleport>
    </template>
  </CrudPage>
</template>
