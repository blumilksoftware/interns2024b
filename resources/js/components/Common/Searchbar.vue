<script lang="ts" setup>
import {computed, ref, defineProps} from 'vue'
const isSearchFocused = ref(false)
const searchQuery = ref('')
const props = defineProps({options: {type: Object, default:()=>{}}})
const filteredSchools: any = computed(
  () => {
    return props.options.filter(
      (obj:any) => obj.option.toString().includes(searchQuery.value),
    )
  },
)
const selectedOption = ref('')

const onOptionClick = (obj:any)=>{
  selectedOption.value = obj.option
  isSearchFocused.value = false
}
</script>

<template>
  <div :class="{'scale-y-100 max-h-80': isSearchFocused}" class="overflow-hidden duration-200 max-h-12 flex flex-col mt-2 bg-white/30 placeholder:text-gray-400 rounded-[.5rem] ring-2 ring-primary/30 ring-inset">
    <div class="flex h-inherit items-center justify-center duration-200 rounded-[.5rem]" :class="{'ring-inset ring ring-primary':isSearchFocused}">
      <div class="h-full items-center justify-center px-3">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5 stroke-primary/50">
          <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
        </svg>
      </div>

      <input
        :value="isSearchFocused ? searchQuery : selectedOption"
        class="outline-none py-3 bg-transparent w-full text-gray-900"
        required name="search-schools" :class="{'cursor-pointer' : !isSearchFocused}"
        type="text"
        :placeholder="selectedOption"
        @input="(e:any)=>searchQuery=e.target.value"
        @focus="isSearchFocused=true"
        @blur="isSearchFocused=false"
      >
    </div>

    <Transition>
      <div v-show="true" class="m-0.5 mt-0 py-2 overflow-auto">
        <div v-if="filteredSchools.length>0">
          <span v-for="obj in filteredSchools"
                :key="obj.id"
                class="cursor-pointer block px-4 py-2 hover:bg-primary/10 text-[0.9rem]"
                @mousedown="onOptionClick(obj)"
          >{{ obj.option }}</span>
        </div>
        <span v-else class="block px-4 py-2 text-sm">
          Nie znaleziono szkoły
        </span>
      </div>
    </Transition>
  </div>
</template>
