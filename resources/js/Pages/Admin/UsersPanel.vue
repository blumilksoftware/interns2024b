<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import CrudPage from '@/components/Crud/CrudPage.vue'
import CrudInput from '@/components/Crud/CrudInput.vue'
import vDynamicInputWidth from '@/Helpers/vDynamicInputWidth'
import CrudSchoolInput from '@/components/Crud/CrudSchoolInput.vue'
import WarningMessageBox from '@/components/Common/WarningMessageBox.vue'
import { ref, computed, onMounted } from 'vue'
import RequestWrapper from '@/components/Common/RequestWrapper.vue'
import { type PageProps } from '@/Types/PageProps'
import { UserMinusIcon, PencilIcon } from '@heroicons/vue/24/outline'
import ArchiveDynamicIcon from '@/components/Icons/ArchiveDynamicIcon.vue'
import Expand from '@/components/Common/Expand.vue'
import { schoolsFetcher } from '@/Helpers/SchoolsFetcher'

const props = defineProps<{ users: Pagination<User> } & PageProps>()
const users = computed(() => ({
  ...props.users,
  data: props.users.data.map(user => ({ ...user, school_id: user.school.id })),
}))

const showWarning = ref<Record<string, boolean>>({})
const showAnonymizedUsers = ref<boolean>(true)
const customQueries = computed(() => ({ anonymized: !showAnonymizedUsers.value }))

const sortOptions: SortOption[] = [
  { text: 'Po nazwie (A–Z)', key: 'firstname' },
  { text: 'Po nazwie (Z–A)', key: 'firstname', desc: true },
  { text: 'Po szkole (A-Z)', key: 'school' },
  { text: 'Po szkole (Z-A)', key: 'school',  desc: true },
  { text: 'Od najnowszych', key: 'created_at' },
  { text: 'Od najstarszych', key: 'created_at', desc: true },
  { text: 'Po dacie modyfikacji (rosnąco)', key: 'updated_at' },
  { text: 'Po dacie modyfikacji (malejąco)', key: 'updated_at', desc: true },
]

const schools = ref<Pagination<School>>({ data: [] } as any)
const isFetchingSchools = ref(false)
const searchErrorMessage = ref('')

onMounted(fetchSchools)

async function fetchSchools(search?: string) {
  [
    schools.value,
    isFetchingSchools.value,
    searchErrorMessage.value,
  ] = await schoolsFetcher(schools.value, search)
}
</script>

<template>
  <Head title="Uczniowie - Panel administracyjny" />

  <CrudPage
    :options="sortOptions"
    :items="users"
    :custom-queries="customQueries"
    resource-name="users"
    disable-edit-button
  >
    <template #actions>
      <button
        :title="`${showAnonymizedUsers ? 'Wyświetl' : 'Schowaj'} zanonimizowanych studentów`"
        class="flex gap-2 hover:bg-primary/5 hover:text-primary duration-200 p-2 rounded-lg"
        @click="showAnonymizedUsers = !showAnonymizedUsers"
      >
        <ArchiveDynamicIcon :active="showAnonymizedUsers" />

        <span class="hidden sm:block">
          {{ showAnonymizedUsers ? 'Wyświetl' : 'Schowaj' }} zanonimizowanych studentów
        </span>
      </button>

      <Expand />
    </template>

    <template #title="{item, editing, errors: editErrors}">
      <div class="flex gap-1">
        <label
          v-if="editing"
          for="firstname"
        >
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

        <label
          v-if="editing"
          for="surname"
          :class="{'ml-1':editing }"
        >
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

      <div
        v-if="editErrors.firstname"
        class="text-red text-sm truncate"
      >
        {{ editErrors.firstname }}
      </div>

      <div
        v-if="editErrors.surname"
        class="text-red text-sm truncate"
      >
        {{ editErrors.surname }}
      </div>
    </template>

    <template #itemActions="{item, editMode}">
      <button
        v-if="!item.isAnonymized"
        title="Edytuj"
        @click="() => editMode(true)"
      >
        <PencilIcon class="icon slide-up-animation" />
      </button>

      <button
        v-if="user?.isSuperAdmin && !item.isAnonymized"
        title="Anonimizuj"
        @click="showWarning[item.id] = true"
      >
        <UserMinusIcon class="icon slide-up-animation text-red hover:text-red-500" />
      </button>
    </template>

    <template #itemData="{ item, errors, editing }">
      <CrudInput
        v-model="item.email"
        name="email"
        label="E-mail:"
        :error="errors.email"
        :editing="editing"
      />

      <CrudSchoolInput
        :editing="editing"
        :school="item.school"
        :schools="schools"
        :error="errors.school_id"
        :is-fetching="isFetchingSchools"
        :no-fetch-text="searchErrorMessage"
        @request-data="fetchSchools"
        @change="(school:School) => [item.school, item.school_id] = [school, school.id]"
      />

      <Teleport to="body">
        <WarningMessageBox
          :open="!!showWarning[item.id]"
          @close="showWarning[item.id] = false"
        >
          <template #message>
            <b class="text-[1.1rem] text-gray-900">
              Czy na pewno chcesz zanonimizować "{{ item.firstname }}"?
            </b>

            <p class="text-gray-500">
              Dane zostaną utracone bezpowrotnie.
            </p>
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
