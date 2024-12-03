<script setup lang="ts" generic="T extends { id: string | number }, N">
import { PlusCircleIcon } from '@heroicons/vue/20/solid'
import { ArrowsUpDownIcon } from '@heroicons/vue/24/outline'
import { vAutoAnimate } from '@formkit/auto-animate'
import Expand from '@/components/Common/Expand.vue'
import Dropdown from '@/components/Common/Dropdown.vue'
import { type Errors } from '@inertiajs/core'
import { ref, watch } from 'vue'
import Button from '@/components/Common/Button.vue'
import CrudNewItem from '@/components/Crud/CrudNewItem.vue'
import CrudItem from '@/components/Crud/CrudItem.vue'
import { useSorter } from '@/Helpers/Sorter'
import SearchBar from '@/components/Crud/SearchBar.vue'
import Pagination from '@/components/Common/Pagination.vue'
import { useParams } from '@/Helpers/Params'
import NoContent from '@/components/Common/NoContent.vue'

const props = defineProps<{
  items: Pagination<T>
  options: SortOption[]
  customQueries?: () => string[]
  customSearch?: (text: string | undefined) => string | undefined
  displaySearchInLowerCase?: boolean
  resourceName: string
  newButtonText?: string
  newItemData?: N
  searchBarPlaceholder?: string
  deletable?: boolean
  creatable?: boolean
  mobileNav?: boolean
}>()

defineSlots<{
  actions: () => any
  searchActions: () => any
  title: (scope: { item: T, editing: boolean, errors: Errors }) => any
  deleteMessage: (scope: { item: T }) => any
  item: (scope: { item: T }) => any
  newItem: (scope: { newItemMode: boolean }) => any
  itemActions: (scope: { item: T }) => any
  itemData: (scope: { item: T, editing: boolean, errors: Errors }) => any
  noContent: (scope: { search: boolean }) => any
}>()

const pagination = props.items
const newItemMode = ref(false)
const params = useParams()
const searchValue = ref<string | undefined>(params.search)

function handleSearch(text: string | undefined) {
  if (props.customSearch) {
    searchValue.value = props.customSearch(text)
  }
  else {
    searchValue.value = text
  }
}

const isSearchbarEmpty = ref(!params.search)
watch(() => props.items.data, () => {
  isSearchbarEmpty.value = !searchValue.value
})

const [query, options] = useSorter(props.options, searchValue, props.customQueries)
</script>

<template>
  <div class="flex flex-col w-full pb-3">
    <div data-name="toolbar" class="flex xs:flex-row px-4 gap-2" :class="{ 'flex-col': mobileNav }">
      <Dropdown pointer-position="left" class-btn="rounded-lg" class="mr-auto" :options="options" title="Sortuj">
        <div class="flex gap-2 hover:bg-primary/5 hover:text-primary duration-200 p-2 rounded-lg h-fit">
          <ArrowsUpDownIcon class="size-6" />
          <span class="hidden sm:block">Sortuj</span>
        </div>
      </Dropdown>

      <slot name="actions">
        <Expand class="hidden sm:block" />
      </slot>

      <Button
        v-if="creatable"
        class="rounded-xl"
        :class="{'cursor-pointer': !newItemMode}"
        button-class="pl-3 font-bold"
        :disabled="newItemMode"
        @click="newItemMode = true"
      >
        <PlusCircleIcon class="size-6 text-white" /> {{ newButtonText }}
      </Button>
    </div>

    <div class="flex w-full px-4 mt-2 justify-between gap-2">
      <SearchBar :placeholder="searchBarPlaceholder" class="w-full" :default-value="displaySearchInLowerCase ? params.search?.toLowerCase() : params.search" @search="handleSearch" />
      <slot name="searchActions" />
      <Pagination v-if="items.data.length > 0" :data="pagination" :query="query" />
    </div>

    <div v-auto-animate class="flex flex-col gap-4 p-4">
      <template v-if="newItemMode">
        <slot name="newItem" :new-item-mode="newItemMode">
          <CrudNewItem :new-item-data="newItemData" :resource-name="resourceName" @done="newItemMode = false">
            <template #title="data">
              <slot name="title" v-bind="data as any" />
            </template>

            <template #data="data">
              <slot name="itemData" v-bind="data as any" />
            </template>
          </CrudNewItem>
        </slot>
      </template>

      <slot v-for="item of items.data" :key="item.id" name="item" :item="item">
        <CrudItem :item="item" :resource-name="resourceName" :deletable="deletable">
          <template #deleteMessage="data">
            <slot name="deleteMessage" v-bind="data" />
          </template>

          <template #title="data">
            <slot name="title" v-bind="data" />
          </template>

          <template #actions>
            <slot name="itemActions" :item="item" />
          </template>

          <template #data="data">
            <slot name="itemData" v-bind="data" />
          </template>
        </CrudItem>
      </slot>

      <template v-if="items.data.length === 0">
        <slot name="noContent" :search="!isSearchbarEmpty">
          <NoContent :description="!isSearchbarEmpty ? `Wygląda na to że nie mamy tego czego szukasz.` : undefined">
            <div v-if="isSearchbarEmpty">
              <Button class="rounded-xl" button-class="pl-3 font-bold" @click="newItemMode = true">
                <PlusCircleIcon class="size-6 text-white" /> {{ newButtonText }}
              </Button>
            </div>
          </NoContent>
        </slot>
      </template>
    </div>

    <div v-if="items.data.length > 0" class="flex justify-center">
      <Pagination :data="pagination" :query="query" />
    </div>
  </div>
</template>
