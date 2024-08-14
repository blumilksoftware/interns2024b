<script lang="ts" setup>
import Checkbox from '@/components/Common/Checkbox.vue'
function submitForm() {
  // ...  
}

import {ref, computed} from 'vue'

// example school data
const providedSchoolsData = ref([
  {id: 0, data: 'Szkoła #1'},
  {id: 1, data: 'Szkoła #2'},
  {id: 2, data: 'Szkoła #3'},
  {id: 3, data: 'Szkoła #4'},
  {id: 4, data: 'Szkoła #5'},
  {id: 5, data: 'Szkoła #6'},
  {id: 6, data: 'Szkoła #7'},
  {id: 7, data: 'Szkoła #8'},
])

const isSearchFocused = ref(false)
const schoolsSearchBar = ref('')
const filteredSchools: any = computed(
  () => {
    return providedSchoolsData.value.filter(
      obj => obj.data.toString().includes(schoolsSearchBar.value),
    )
  },
)
const selectedSchool = ref('')

const onListOptionClick = (obj:any)=>{
  schoolsSearchBar.value=obj.data
  selectedSchool.value=obj.data
}
</script>

<template>
  <form class="row-start-1 col-start-1 space-y-6" method="POST" @submit.prevent="submitForm">
    <div class="flex flex-row gap-6">
      <div class="w-full">
        <label for="name" class="text-sm font-medium leading-6 text-gray-900">Imię</label>
        <div class="mt-2 w-full">
          <input id="name" required name="name" type="text"
                 class="duration-200 ring-inset outline-none focus:ring focus:ring-primary bg-white/30 rounded-lg w-full p-3 text-gray-900 ring-2 ring-primary/30 placeholder:text-gray-400"
          >
        </div>
      </div>

      <div class="w-full">
        <label for="surname" class="text-sm font-medium leading-6 text-gray-900">Nazwisko</label>
        <div class="mt-2 w-full">
          <input id="surname" required name="surname" type="text"
                 class="duration-200 ring-inset outline-none focus:ring focus:ring-primary bg-white/30 rounded-lg w-full p-3 text-gray-900 ring-2 ring-primary/30 placeholder:text-gray-400"
          >
        </div>
      </div>
    </div>

    <div>
      <div class="flex items-center justify-between">
        <label for="search-schools" class="block text-sm font-medium leading-6 text-gray-900">Wybierz szkołę</label>
      </div>
      <div :class="{'scale-y-100 max-h-80':isSearchFocused}" class="overflow-hidden max-h-12 flex flex-col mt-2 bg-white/30 placeholder:text-gray-400 rounded-[.5rem] ring-2 ring-primary/30 ring-inset">
        <input 
          v-model="schoolsSearchBar"
          class="outline-none focus:duration-200 focus:ring-inset focus:ring focus:ring-primary rounded-[.5rem] p-3 bg-transparent w-full text-gray-900" required name="search-schools"
          :class="{'cursor-pointer' : !isSearchFocused}"
          type="text"
          @focus="isSearchFocused = true; schoolsSearchBar=''"
          @blur="isSearchFocused = false; schoolsSearchBar=selectedSchool"
        >
        <Transition>
          <div v-show="true" class="m-0.5 mt-0 py-2 overflow-auto">
            <div v-if="filteredSchools.length>0">
              <span v-for="obj in filteredSchools"
                    :key="obj.id"
                    class="cursor-pointer block px-4 py-2 hover:bg-primary/10 text-[0.9rem]"
                    @mousedown="onListOptionClick(obj)"
              >{{ obj.data }}</span>
            </div>
            <span v-else class="block px-4 py-2 text-sm">
              Nie znaleziono szkoły
            </span>
          </div>
        </Transition>
      </div>
    </div>

    <div>
      <label for="email" class="block text-sm font-medium leading-6 text-gray-900">E-mail</label>
      <div class="mt-2">
        <input id="email" required name="email" type="email" autocomplete="email"
               class="duration-200 ring-inset outline-none focus:ring focus:ring-primary bg-white/30 rounded-lg w-full p-3 text-gray-900 ring-2 ring-primary/30 placeholder:text-gray-400"
        >
      </div>
    </div>

    <div>
      <div class="flex items-center justify-between">
        <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Hasło</label>
      </div>
      <div class="mt-2">
        <input id="password" required name="password" type="password" autocomplete="current-password"
               class="duration-200 ring-inset outline-none focus:ring focus:ring-primary bg-white/30 rounded-lg w-full p-3 text-gray-900 ring-2 ring-primary/30 placeholder:text-gray-400"
        >
      </div>
    </div>

    <div class="mx-2 mt-4 flex flex-row items-center gap-4">
      <Checkbox />
      <p class="w-fit text-sm text-gray-500">
        Akceptuję
        <a href="#" class="font-semibold leading-6 text-primary hover:primary">regulamin</a> i
        <a href="#" class="font-semibold leading-6 text-primary hover:primary">politykę prywatności</a>
      </p>
    </div>

    <div>
      <button type="submit"
              class="
              rounded-lg text-md flex w-full justify-center bg-primary p-3 font-bold text-white transition hover:bg-primary-950
              focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary"
      >
        Zarejestruj się
      </button>
    </div>
  </form>
</template>
