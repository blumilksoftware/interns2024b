<script lang="ts" setup generic="T extends Option">
import { ref, defineProps, computed, onMounted } from 'vue'
import { onClickOutside, useDebounceFn } from '@vueuse/core'
import { nanoid } from 'nanoid'

const id = ref(nanoid())
const target = ref()
onClickOutside(target, () => isFocused.value = false)
const isFocused = ref(false)
const searchQuery = ref('')
const props = defineProps<{
  options:T[]
  label:string
  ariaLabel?:string
  error?:string
  noResultsText?:string
  isLoadingFinished?:boolean
  pagesEnded?: boolean
  onFetchAdditionalData:(search?:string)=>Promise<void>
}>()
const emit = defineEmits<{ change: [option:T] }>()
const selectedOption = ref<T>()
const options = computed(() =>
  props.options.filter(option =>
    option.text.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    option.title?.toLowerCase().includes(searchQuery.value.toLowerCase()),
  ),
)

const loadingElement = ref<HTMLElement>()
const contentElement = ref<HTMLElement>()

onMounted(() => {
  const observer = new IntersectionObserver(
    entries => {
      async function loadNewContent() {
        if (!contentElement.value) return
        const scrollOffset = contentElement.value.scrollTop
        console.log('scrollOffset', `${contentElement.value.scrollHeight} - ${contentElement.value.scrollTop}`)
        
        await props.onFetchAdditionalData()
        contentElement.value.scrollTop = scrollOffset
      }
      if (entries[0].isIntersecting) {
        loadNewContent()
      }
    },
    { threshold: .0 },
  )

  if (loadingElement.value) {
    observer.observe(loadingElement.value)
  }
})

function onOptionClick(option:T) {
  selectedOption.value = option
  isFocused.value = false
  emit('change', option)
}

function updateQuery(event: any) {
  searchQuery.value = event.currentTarget.value
}

const handleInput = useDebounceFn(() => {
  props.onFetchAdditionalData(searchQuery.value.toLocaleUpperCase())
}, 500)

const showLoading = computed(()=>!searchQuery.value && !props.pagesEnded || searchQuery.value && !props.isLoadingFinished)
</script>

<template>
  <div class="block w-full text-sm font-medium leading-6 text-gray-900 duration-200">
    <label :for="id">{{ label }}</label>
    <div
      ref="target"
      class="mt-2 font-medium text-sm leading-6 text-gray-900 overflow-hidden duration-200 max-h-12 flex flex-col bg-white/30 placeholder:text-gray-400 rounded-lg ring-2 ring-primary/30 ring-inset"
      :class="{'scale-y-100 max-h-80': isFocused, 'ring-red' : error }"
    >
      <div
        class="flex h-inherit items-center justify-center duration-200 rounded-lg"
        :class="{ 'ring-inset ring-2 ring-primary/60' : isFocused, 'ring-red' : error }"
      >
        <div class="h-full items-center justify-center px-3">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5 text-primary/30" :class="{'!text-primary/60': isFocused}">
            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
          </svg>
        </div>
        <input
          :id="id"
          ref="searchElement"
          class="outline-none py-3 bg-transparent w-full text-gray-900"
          autocomplete="off"
          name="search"
          type="text"
          required
          :aria-label="ariaLabel"
          :value="isFocused ? searchQuery : selectedOption?.text"
          :class="{'cursor-pointer' : !isFocused}"
          :placeholder="selectedOption?.text"
          @input="event=>{console.log('astarst') ;updateQuery(event); handleInput()}"
          @focus="isFocused=true"
        >
      </div>

      <Transition>
        <div v-show="isFocused" ref="contentElement" class="m-0.5 mt-0 py-2 overflow-auto">
          <button
            v-for="obj in options"
            :key="obj.key"
            class="cursor-pointer block px-4 py-2 hover:bg-primary/10 outline-none focus:bg-primary/10 text-[0.9rem] w-full text-left"
            @click="onOptionClick(obj)"
            @focus="isFocused=true"
          >
            <b v-if="obj.title">{{ obj.title.toUpperCase() }}</b>
            <p>{{ obj.text }}</p>
          </button>
          <div v-show="showLoading" class="bg-white/50 z-10 w-full h-fit left-0 flex items-center justify-center p-2">
            <div class="inline-block size-8 animate-spin rounded-full border-4 border-solid border-current border-e-transparent align-[-0.125em] text-primary motion-reduce:animate-[spin_1.5s_linear_infinite]" role="status" />
          </div>
          <div v-show="!searchQuery && !pagesEnded" ref="loadingElement" />
          <span v-show="options.length <= 0 && !showLoading" class="block px-4 py-2 text-sm">
            {{ noResultsText }}
          </span>
        </div>
      </Transition>
    </div>
    <div v-if="error" class="text-red">{{ error }}</div>
  </div>
</template>
