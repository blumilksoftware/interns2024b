<script lang="ts" setup>
import { ref, computed, watch } from 'vue'
import { useDebounceFn } from '@vueuse/core'
import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline'
import CustomInput from '@/components/Common/CustomInput.vue'
import InputWrapper from '@/components/QuizzesPanel/InputWrapper.vue'
import vClickOutside from '@/Helpers/vClickOutside'
import List from '@/components/Common/List.vue'

const props = defineProps<{
  options: Array<Option|any>
  label: string
  ariaLabel?: string
  error?: string
  isFetching: boolean
  noResultsText?: string
  noFetchText?: string
}>()
const emit = defineEmits<{ change: [option:Option|any], requestData: [search?: string] }>()

const selectedOption = ref<Option|any>()
const searchQuery = ref('')

const isDebouncing = ref(false)
const isFocused = ref(false)

const options = computed<Option|any>(() => props.options.filter(
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

function onOptionClick(option: Option|any) {
  selectedOption.value = option
  isFocused.value = false
  emit('change', option)
}

watch(
  () => isFocused.value ? '' : selectedOption.value?.text ?? '', 
  search => searchQuery.value = search,
)
</script>

<template>
  <InputWrapper
    class="text-sm font-medium leading-6"
    wrapper-class="gap-2"
    :label="label"
    :error="error"
    column
  >
    <List
      v-click-outside="() => isFocused = false"
      :options
      :opened="isFocused"
      :use-lazy-loading="!searchQuery"
      :fetching="isFetching || isDebouncing"
      :no-fetch-text
      :no-results-text
      @option-click="onOptionClick"
      @lazyload="emit('requestData')"
    >
      <CustomInput
        v-model="searchQuery"
        name="search"
        :aria-label="ariaLabel"
        :class="{'cursor-pointer' : !isFocused}"
        :placeholder="selectedOption?.text"
        @input="handleInput"
        @focus="isFocused = true"
      >
        <template #left>
          <MagnifyingGlassIcon
            class="stroke-2 size-5 text-primary/30"
            :class="{'!text-primary/60': isFocused}"
          />
        </template>
      </CustomInput>
    </List>
  </InputWrapper>
</template>
