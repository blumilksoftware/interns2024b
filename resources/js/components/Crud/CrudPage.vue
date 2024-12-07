<script setup lang="ts" generic="T extends { id: string | number }, N">
import { PlusCircleIcon } from '@heroicons/vue/20/solid'
import { ArrowsUpDownIcon } from '@heroicons/vue/24/outline'
import { vAutoAnimate } from '@formkit/auto-animate'
import Expand from '@/components/Common/Expand.vue'
import Dropdown from '@/components/Common/Dropdown.vue'
import { type Errors } from '@inertiajs/core'
import { computed, ref } from 'vue'
import Button from '@/components/Common/Button.vue'
import CrudNewItem from '@/components/Crud/CrudNewItem.vue'
import CrudItem from '@/components/Crud/CrudItem.vue'
import { useQuery } from '@/Helpers/Query'
import SearchBar from '@/components/Crud/SearchBar.vue'
import PaginationSwitch from '@/components/Common/PaginationSwitch.vue'
import NoContent from '@/components/Common/NoContent.vue'

const props = defineProps<{
  items: Pagination<T>
  options: SortOption[]
  customQueries?: Record<string, any>
  customSearch?: (text: string | undefined) => string | undefined
  displaySearchInLowerCase?: boolean
  resourceName: string
  newButtonText?: string
  newItemData?: N
  searchBarPlaceholder?: string
  searchBarModes?: Mode[]
  deletable?: boolean
  creatable?: boolean
  mobileNav?: boolean
}>()

defineSlots<{
  actions: () => any
  title: (scope: { item: T, editing: boolean, errors: Errors }) => any
  deleteMessage: (scope: { item: T }) => any
  item: (scope: { item: T }) => any
  newItem: (scope: { newItemMode: boolean }) => any
  itemActions: (scope: { item: T }) => any
  itemData: (scope: { item: T, editing: boolean, errors: Errors }) => any
  noContent: (scope: { search: boolean }) => any
}>()

const pagination = computed(() => props.items)
const newItemMode = ref(false)

const { queryParams } = useQuery<Record<string, any>>({ page: 1, ...props.customQueries })

const isSearchbarEmpty = computed(() => !queryParams.search)

function handleSearch(text: string | undefined, mode?: string) {
  queryParams.search = props.customSearch ? props.customSearch(text) : text
  queryParams.mode = mode?.toUpperCase()
}

function pageSwitch(isLeftSwitch: boolean) {
  const currentPage = pagination.value.current_page ?? queryParams.page
  queryParams.page = isLeftSwitch ? currentPage -1 : currentPage + 1
}
</script>

<template>
  <div class="flex flex-col w-full pb-3">
    <div
      data-name="toolbar"
      class="flex flex-col sm:flex-row px-4 gap-2"
      :class="{ 'flex-col': mobileNav }"
    >
      <Dropdown
        pointer-position="left"
        class-btn="rounded-lg"
        class="mr-auto"
        title="Sortuj"
        :options="props.options"
        @option-click="(option: SortOption) => {
          queryParams.sort = option.key
          queryParams.order = option.desc ? 'desc' : 'asc'
        }"
      >
        <div class="flex gap-2 hover:bg-primary/5 hover:text-primary duration-200 p-2 rounded-lg h-fit">
          <ArrowsUpDownIcon class="size-6" />

          <span class="hidden sm:block">
            Sortuj
          </span>
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
      <SearchBar 
        :placeholder="searchBarPlaceholder"
        class="w-full" 
        :default-value="displaySearchInLowerCase ? queryParams.search?.toLowerCase() : queryParams.search"
        :modes="searchBarModes"
        @search="handleSearch"
      />

      <PaginationSwitch
        v-if="items.data.length > 0"
        :disabled-left="!pagination.links.prev"
        :disabled-right="!pagination.links.next"
        :from="pagination.meta.from ?? 0"
        :to="pagination.meta.to ?? 0"
        @switch="pageSwitch"
      />
    </div>

    <div
      v-auto-animate
      class="flex flex-col gap-4 p-4"
    >
      <template v-if="newItemMode">
        <slot
          name="newItem"
          :new-item-mode="newItemMode"
        >
          <CrudNewItem
            :new-item-data="newItemData"
            :resource-name="resourceName"
            @done="newItemMode = false"
          >
            <template #title="data">
              <slot
                name="title"
                v-bind="(data as any)"
              />
            </template>

            <template #data="data">
              <slot
                name="itemData"
                v-bind="(data as any)"
              />
            </template>
          </CrudNewItem>
        </slot>
      </template>

      <slot
        v-for="item of items.data"
        :key="item.id"
        name="item"
        :item="item"
      >
        <CrudItem
          :item="item"
          :resource-name="resourceName"
          :deletable="deletable"
        >
          <template #deleteMessage="data">
            <slot
              name="deleteMessage"
              v-bind="data"
            />
          </template>

          <template #title="data">
            <slot
              name="title"
              v-bind="data"
            />
          </template>

          <template #actions>
            <slot
              name="itemActions"
              :item="item"
            />
          </template>

          <template #data="data">
            <slot
              name="itemData"
              v-bind="data"
            />
          </template>
        </CrudItem>
      </slot>

      <template v-if="items.data.length === 0">
        <slot
          name="noContent"
          :search="!isSearchbarEmpty"
        >
          <NoContent :description="!isSearchbarEmpty ? `Wygląda na to że nie mamy tego czego szukasz.` : undefined">
            <div v-if="isSearchbarEmpty">
              <Button
                class="rounded-xl"
                button-class="pl-3 font-bold"
                @click="newItemMode = true"
              >
                <PlusCircleIcon class="size-6 text-white" /> {{ newButtonText }}
              </Button>
            </div>
          </NoContent>
        </slot>
      </template>
    </div>

    <div
      v-if="items.data.length > 0"
      class="flex justify-center"
    >
      <PaginationSwitch
        v-if="items.data.length > 0"
        :disabled-left="!pagination.links.prev"
        :disabled-right="!pagination.links.next"
        :from="pagination.meta.from ?? 0"
        :to="pagination.meta.to ?? 0"
        @switch="pageSwitch"
      />
    </div>
  </div>
</template>
