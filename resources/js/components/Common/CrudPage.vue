<script setup lang="ts" generic="T extends Sortable & { id: string | number }">
import { PlusCircleIcon } from '@heroicons/vue/20/solid'
import { ArrowsUpDownIcon } from '@heroicons/vue/24/outline'
import { vAutoAnimate } from '@formkit/auto-animate'
import FormButton from '@/components/Common/FormButton.vue'
import Expand from '@/components/Common/Expand.vue'
import Dropdown from '@/components/Common/Dropdown.vue'
import {Errors, RequestPayload} from '@inertiajs/core'
import {useSorter} from "@/Helpers/Sorter";
import CrudItem from "@/components/Common/CrudItem.vue";

const props = defineProps<{
  items: T[]
  options:  SortOptionConstructor[]
  resourceName: string,
  newButtonText: string
  newItemData: RequestPayload
  deletable?: boolean
}>()

defineSlots<{
  actions: () => any,
  title: (scope: { item: T, editing: boolean, errors: Errors }) => any,
  deleteMessage: (scope: { item: T }) => any,
  item: (scope: { item: T }) => any,
  itemActions: () => any,
  itemData: (scope: { item: T, editing: boolean, errors: Errors }) => any
}>()

const [items, options] = useSorter(props.resourceName, () => props.items, props.options)

</script>

<template>
  <div class="flex flex-col w-full pb-3">
    <div data-name="toolbar" class="flex px-4 gap-1 sm:gap-2">
      <Dropdown class-btn="rounded-lg" :options="options" title="Sortuj">
        <div class="flex gap-2 hover:bg-primary/5 hover:text-primary duration-200 p-2 rounded-lg">
          <ArrowsUpDownIcon class="size-6" />
          <span class="hidden sm:block">Sortuj</span>
        </div>
      </Dropdown>

      <Expand />

      <slot name="actions" />

      <FormButton
        class="rounded-xl"
        button-class="pl-3 font-bold"
        method="post"
        :href="`/admin/${resourceName}`"
        :data="newItemData"
      >
        <PlusCircleIcon class="size-6 text-white" /> {{newButtonText}}
      </FormButton>
    </div>

    <div v-auto-animate class="flex flex-col gap-4 p-4">
      <slot v-for="item of items" :key="item.id" name="item" :item="item">
        <CrudItem :item="item" :resource-name="resourceName" :deletable="deletable">
          <template #deleteMessage="{item}">
            <slot name="deleteMessage" :item="item" />
          </template>

          <template #title="{item, errors, editing}">
            <slot name="title" :item="item" :errors="errors" :editing="editing" />
          </template>

          <template #actions>
            <slot name="itemActions" />
          </template>

          <template #data="{item, errors, editing}">
            <slot name="itemData" :item="item" :errors="errors" :editing="editing" />
          </template>
        </CrudItem>
      </slot>
    </div>
  </div>
</template>
