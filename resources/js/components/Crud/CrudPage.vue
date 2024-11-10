<script setup lang="ts" generic="T extends Sortable & { id: string | number }, N extends Omit<T, 'createdAt' | 'id' | 'updatedAt'>">
import { PlusCircleIcon } from '@heroicons/vue/20/solid'
import { ArrowsUpDownIcon } from '@heroicons/vue/24/outline'
import { vAutoAnimate } from '@formkit/auto-animate'
import Expand from '@/components/Common/Expand.vue'
import Dropdown from '@/components/Common/Dropdown.vue'
import {type Errors} from '@inertiajs/core'
import {useSorter} from '@/Helpers/Sorter'
import {ref} from 'vue'
import Button from '@/components/Common/Button.vue'
import CrudNewItem from '@/components/Crud/CrudNewItem.vue'
import CrudItem from '@/components/Crud/CrudItem.vue'

const props = defineProps<{
  items: T[]
  options:  SortOptionConstructor[]
  resourceName: string
  newButtonText: string
  newItemData: N
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
}>()

const [items, options] = useSorter(props.resourceName, () => props.items, props.options)
const newItemMode = ref(false)

</script>

<template>
  <div class="flex flex-col w-full pb-3">
    <div data-name="toolbar" class="flex xs:flex-row px-4 gap-1 sm:gap-2" :class="{ 'flex-col': mobileNav }">
      <Dropdown pointer-position="left" class-btn="rounded-lg" class="mr-auto" :options="options" title="Sortuj">
        <div class="flex gap-2 hover:bg-primary/5 hover:text-primary duration-200 p-2 rounded-lg">
          <ArrowsUpDownIcon class="size-6" />
          <span class="hidden sm:block">Sortuj</span>
        </div>
      </Dropdown>

      <slot name="actions">
        <Expand />
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


      <slot v-for="item of items" :key="item.id" name="item" :item="item">
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
    </div>
  </div>
</template>
