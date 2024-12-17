<script lang="ts" setup>
import { computed, ref, watch } from 'vue'
import CrudInput from '@/components/Crud/CrudInput.vue'
import { nanoid } from 'nanoid'
import { useDebounceFn } from '@vueuse/core'
import List from '@/components/Common/List.vue'
import vDynamicInputWidth from '@/Helpers/vDynamicInputWidth'
import vClickOutside from '@/Helpers/vClickOutside'

const props = defineProps<{
  editing: boolean
  school?: School
  schools: Pagination<School>
  error?: string
  isFetching: boolean
  noFetchText?: string
}>()
const emit = defineEmits<{ change: [option:School], requestData: [search?: string] }>()

const value = computed(() => `${props.school?.city} - ${props.school?.name}`)
const searchQuery = ref('')

const isDebouncing = ref(false)
const isFocused = ref(false)

const options = computed(() => props.schools.data.map(schoolToSchoolOption).filter(
  option => option.text.toLocaleUpperCase().includes(searchQuery.value.toLocaleUpperCase()) ||
      option.title?.toLocaleUpperCase().includes(searchQuery.value.toLocaleUpperCase()),
))

const debouncedRequest = useDebounceFn(
  () => {
    emit('requestData', searchQuery.value.toLocaleUpperCase())
    isDebouncing.value = false
  }, 
  500,
)

function handleInput(value: string) {
  searchQuery.value = value
  isDebouncing.value = true
  debouncedRequest()
}

watch(
  () => isFocused.value ? '' : value.value ?? '', 
  search => searchQuery.value = search,
)


function onOptionClick(school: School) {
  isFocused.value = false
  emit('change', school)
}

function schoolToSchoolOption(school: School): School & Option {
  return {
    ...school,
    key: nanoid(), 
    title: school.city,
    text: school.name, 
  }
}
</script>

<template>
  <CrudInput
    v-model="value"
    v-click-outside="()=>isFocused=false"
    label="Szkoła:"
    :editing
  >
    <div class="flex flex-col">
      <input
        v-dynamic-input-width
        :value="searchQuery"
        autocomplete="off"
        name="school"
        type="text"
        class="text-md transition-colors h-fit w-full outline-none font-bold border-b border-transparent bg-transparent focus:border-b-primary"
        :class="{
          'border-b-primary/30 hover:border-b-primary/60 text-primary duration-200 transition-colors' : editing,
          'border-b-red' : !!error,
          'cursor-pointer' : !isFocused && editing
        }"
        :disabled="!editing"
        :placeholder="value"
        @input="(e:any)=>handleInput(e.currentTarget.value)"
        @focus="isFocused=true"
      >

      <Transition>
        <List
          v-if="isFocused"
          no-results-text="Nie znaleziono szkoły"
          class="mt-2 bg-white/70 backdrop-blur rounded-lg"
          :options
          :opened="isFocused"
          :use-lazy-loading="!searchQuery"
          :fetching="isFetching || isDebouncing"
          :no-fetch-text
          @option-click="onOptionClick"
          @lazyload="emit('requestData')"
        />
      </Transition>
    </div>
  </CrudInput>
</template>
