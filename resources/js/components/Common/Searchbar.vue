<script lang="ts" setup>
import {computed, ref, defineProps} from 'vue'
const isSearchFocused = ref(false)
const schoolsSearchBar = ref('')
const props = defineProps({options: {type: Object, default:()=>{}}})
const filteredSchools: any = computed(
  () => {
    return props.options.filter(
      (obj:any) => obj.option.toString().includes(schoolsSearchBar.value),
    )
  },
)
const selectedSchool = ref('')

const onListOptionClick = (obj:any)=>{
  schoolsSearchBar.value=obj.option
  selectedSchool.value=obj.option
}
</script>

<template>
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
          >{{ obj.option }}</span>
        </div>
        <span v-else class="block px-4 py-2 text-sm">
          Nie znaleziono szkoły
        </span>
      </div>
    </Transition>
  </div>
</template>
