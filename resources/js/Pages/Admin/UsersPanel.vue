<script setup lang="ts">
import {Head} from '@inertiajs/vue3'
import CrudPage from '@/components/Crud/CrudPage.vue'
import CrudInput from '@/components/Crud/CrudInput.vue'
import vDynamicInputWidth from '@/Helpers/vDynamicInputWidth'
import CrudSchoolInput from '@/components/Crud/CrudSchoolInput.vue'
import WarningMessageBox from '@/components/Common/WarningMessageBox.vue'
import {computed, ref} from 'vue'
import RequestWrapper from '@/components/Common/RequestWrapper.vue'
import {type PageProps} from '@/Types/PageProps'
import { TrashIcon, PencilIcon } from '@heroicons/vue/24/outline'
import ArchiveDynamicIcon from '@/components/Icons/ArchiveDynamicIcon.vue'
import Expand from '@/components/Common/Expand.vue'
import CrudItem from '@/components/Crud/CrudItem.vue'

const props = defineProps<{
  users: User[]
  schools: School[]
} & PageProps>()

const users = computed(() => props.users.map(user => ({ ...user, school_id: user.school.id })))

const showWarning = ref<Record<string, boolean>>({})
const showAnonymizedUsers = ref<boolean>(true)

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
    :items="users"
    resource-name="users"
    new-button-text="Dodaj użytkownika"
    :new-item-data="{
      name: 'Nowy Użytkownik',
      surname: '',
      email: '',
      school_id: 0,
      school: null as any,
      isAnonymized: false,
      isAdmin: false,
      isSuperAdmin: false
    }"
    mobile-nav
  >
    <template #title="{item, editing, errors: editErrors}">
      <div class="flex gap-1">
        <label v-if="editing" for="name">
          Imie:
        </label>

        <input
          id="name"
          v-model="item.name"
          v-dynamic-input-width
          type="text"
          name="name"
          autocomplete="off"
          class="text-md transition-none h-fit w-full outline-none font-bold border-b border-transparent bg-transparent focus:border-b-primary"
          :class="{
            'border-b-primary/30 hover:border-b-primary/60 text-primary text-center' : editing,
            'border-b-red' : !!editErrors.name,
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

    <template #item="{item}">
      <CrudItem v-if="!showAnonymizedUsers || !item.isAnonymized" :item="item" resource-name="users">
        <template #actions="{editMode}">
          <button v-if="!item.isAnonymized" title="Edytuj" @click="editMode(true)">
            <PencilIcon class="icon slide-up-animation" />
          </button>

          <button v-if="user?.isSuperAdmin && !item.isAnonymized" title="Anonimizuj" @click="showWarning[item.id] = true">
            <TrashIcon class="icon slide-up-animation text-red hover:text-red-500" />
          </button>
        </template>

        <template #data="data">
          <WarningMessageBox :open="!!showWarning[data.item.id]" @close="showWarning[data.item.id] = false">
            <template #message>
              <b class="text-[1.1rem] text-gray-900">Czy na pewno chcesz zanonimizować "{{ data.item.name }}"?</b>
              <p class="text-gray-500">Dane zostaną utracone bezpowrotnie.</p>
            </template>

            <template #buttons>
              <RequestWrapper
                class="bg-red font-bold text-white rounded-lg px-4 py-2"
                title="Anonimizuj"
                method="patch"
                :href="`/admin/users/${data.item.id}/anonymize`"
                @click="showWarning[data.item.id] = false"
              >
                Anonimizuj
              </RequestWrapper>
            </template>
          </WarningMessageBox>

          <div class="flex flex-col duration-200 min-h-6.5 gap-2" :class="{'text-sm text-gray-600': !data.editing}">
            <CrudInput
              name="email"
              label="Email:"
              :error="data.errors.email"
              v-model="data.item.email"
              :editing="data.editing"
            />

            <div>
              <CrudSchoolInput
                :value="data.item.school?.id"
                :schools="schools"
                :errors="data.errors"
                :editing="data.editing"
                @change="school => data.item.school_id = school"
              />
            </div>
          </div>
        </template>
      </CrudItem>
    </template>
  </CrudPage>
</template>
