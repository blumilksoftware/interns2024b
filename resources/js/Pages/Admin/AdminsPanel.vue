<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import CrudPage from '@/components/Crud/CrudPage.vue'
import CrudInput from '@/components/Crud/CrudInput.vue'
import vDynamicInputWidth from '@/Helpers/vDynamicInputWidth'
import {type PageProps} from '@/Types/PageProps'
import { computed } from 'vue'

const props = defineProps<{ users: Pagination<User> } & PageProps>()
const admins = computed(() => ({
  ...props.users,
  data: props.users.data.map(admin => ({...admin, school_id: admin.school.id, password: ''})),
}))

const sortOptions: SortOption[] = [
  { text: 'Po nazwie (A–Z)', key: 'firstname' },
  { text: 'Po nazwie (Z–A)', key: 'firstname', desc: true },
  { text: 'Od najnowszych' , key: 'created_at' },
  { text: 'Od najstarszych', key: 'created_at', desc: true },
  { text: 'Po dacie modyfikacji (rosnąco)', key: 'updated_at' },
  { text: 'Po dacie modyfikacji (malejąco)', key: 'updated_at', desc: true },
]
</script>

<template>
  <Head title="Administratorzy - Panel administracyjny" />

  <CrudPage
    :options="sortOptions"
    :items="admins"
    resource-name="admins"
    new-button-text="Dodaj administratora"
    :new-item-data="{
      name: 'Nowy Administrator',
      surname: '',
      email: '',
      school_id: 1,
      password: '',
      school: null,
      isAnonymized: false,
      isAdmin: false,
      isSuperAdmin: false
    }"
    deletable
    creatable
  >
    <template #deleteMessage="{item}">
      <b class="text-[1.1rem] text-gray-900">Czy na pewno chcesz usunąć "{{ item.firstname }} {{ item.surname }}"?</b>
      <p class="text-gray-500">Administrator zostanie usunięty bezpowrotnie.</p>
    </template>

    <template #title="{item, editing, errors: editErrors}">
      <div class="flex gap-1">
        <label v-if="editing" for="firstname">
          Imię:
        </label>

        <input
          id="firstname"
          v-model="item.firstname"
          v-dynamic-input-width
          type="text"
          name="firstname"
          autocomplete="off"
          class="text-md h-fit w-full outline-none font-bold border-b border-transparent bg-transparent focus:border-b-primary"
          :class="{
            'border-b-primary/30 hover:border-b-primary/60 text-primary text-center duration-200 transition-colors' : editing,
            'border-b-red' : !!editErrors.firstname,
          }"
          :disabled="!editing"
        >

        <label v-if="editing" for="surname">
          Nazwisko:
        </label>

        <input
          id="surname"
          v-model="item.surname"
          v-dynamic-input-width
          type="text"
          name="surname"
          autocomplete="off"
          class="text-md h-fit w-full outline-none font-bold border-b border-transparent bg-transparent focus:border-b-primary"
          :class="{
            'border-b-primary/30 hover:border-b-primary/60 text-primary text-center duration-200 transition-colors' : editing,
            'border-b-red' : !!editErrors.surname,
          }"
          :disabled="!editing"
        >
      </div>

      <div v-if="editErrors.firstname" class="text-red text-sm truncate">
        {{ editErrors.firstname }}
      </div>

      <div v-if="editErrors.surname" class="text-red text-sm truncate">
        {{ editErrors.surname }}
      </div>
    </template>

    <template #itemData="data">
      <div class="flex flex-col duration-200 min-h-6.5 gap-2 pt-2" :class="{'text-sm text-gray-600': !data.editing}">
        <CrudInput
          v-model="data.item.email"
          name="email"
          label="E-mail:"
          :error="data.errors.email"
          :editing="data.editing"
        />

        <CrudInput
          v-model="data.item.password"
          name="password"
          label="Hasło:"
          password
          :error="data.errors.password"
          :editing="data.editing"
        />
      </div>
    </template>
  </CrudPage>
</template>
