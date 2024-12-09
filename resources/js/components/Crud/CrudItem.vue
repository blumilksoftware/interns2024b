<script setup lang="ts" generic="T extends { id: string | number }">
import useRequestResolution from '@/Helpers/RequestResolution'
import InputWrapper from '@/components/QuizzesPanel/InputWrapper.vue'
import RequestWrapper from '@/components/Common/RequestWrapper.vue'
import { ref, watch } from 'vue'
import { CheckIcon, PencilIcon, TrashIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { vAutoAnimate } from '@formkit/auto-animate'
import vDynamicInputWidth from '@/Helpers/vDynamicInputWidth'
import WarningMessageBox from '@/components/Common/WarningMessageBox.vue'
import { type Errors, type RequestPayload } from '@inertiajs/core'
import { formatDate } from '@/Helpers/Format'

const props = defineProps<{
  item: T
  resourceName: string
  deletable?: boolean
  disableEditButton?: boolean
}>()

const slots = defineSlots<{
  deleteMessage: (scope: { item: T }) => any
  actions: (scope: { showDeleteMsg: () => void, editMode: (enabled: boolean) => void }) => any
  title: (scope: { item: T, editing: boolean, errors: Errors }) => any
  data: (scope: { item: T, editing: boolean, errors: Errors }) => any
}>()

const item = ref<T>(JSON.parse(JSON.stringify(props.item)))
const editing = ref(false)
const { processing, errors } = useRequestResolution()
const showDeleteMessage = ref(false)
</script>

<template>
  <WarningMessageBox :open="showDeleteMessage" @close="showDeleteMessage = false">
    <template #message>
      <slot name="deleteMessage" :item="item as T" />
    </template>

    <template #buttons>
      <RequestWrapper
        class="bg-red font-bold text-white rounded-lg px-4 py-2"
        title="Usuń"
        method="delete"
        :href="`/admin/${resourceName}/${item.id}`"
        @click="showDeleteMessage = false"
      >
        Usuń
      </RequestWrapper>
    </template>
  </WarningMessageBox>

  <div v-auto-animate class="flex flex-col gap-1 p-5 bg-white/70 border-2 rounded-xl shadow-sm">
    <Transition>
      <div v-show="processing" class="absolute bg-white/50 backdrop-blur-md z-10 size-full left-0 flex items-center justify-center -mt-4 rounded-xl">
        <div class="inline-block size-8 animate-spin rounded-full border-4 border-solid border-current border-e-transparent align-[-0.125em] text-primary motion-reduce:animate-[spin_1.5s_linear_infinite]" role="status" />
      </div>
    </Transition>

    <div class="flex justify-between">
      <div class="size-full md:h-auto px-0">
        <slot name="title" :item="item as T" :editing="editing" :errors="errors">
          <InputWrapper
            v-if="!slots.title"
            :has-content="!!item.name || editing"
            :error="errors.name"
            :show-error="editing"
          >
            <input
              v-model="item.name"
              v-dynamic-input-width
              type="text"
              name="name"
              autocomplete="off"
              class="w-full outline-none font-bold border-b border-transparent duration-200 transition-colors text-lg bg-transparent focus:border-b-primary"
              :class="{
                'border-b-primary/30 duration-200 transition-colors hover:border-b-primary/60 text-primary' : editing,
                'border-b-red' : errors.name
              }"
              :disabled="!editing"
            >
          </InputWrapper>
        </slot>

        <div class="h-full md:hidden pt-1">
          <slot name="data" :item="item as T" :editing="editing" :errors="errors" />
        </div>
      </div>

      <div class="flex flex-col sm:flex-row pl-5 gap-5 h-fit">
        <template v-if="!editing">
          <button v-if="!disableEditButton" title="Edytuj" @click="editing = true">
            <PencilIcon class="icon slide-up-animation" />
          </button>

          <slot name="actions" :show-delete-msg="() => showDeleteMessage = true" :edit-mode="(mode) => editing = mode" />
        </template>

        <template v-if="editing">
          <button
            title="Anuluj edycje"
            @click="() => { item = JSON.parse(JSON.stringify(props.item)); editing = false; errors={} }"
          >
            <XMarkIcon class="icon" />
          </button>

          <RequestWrapper
            title="Zapisz zmiany"
            method="patch"
            preserve-state
            preserve-scroll
            :href="`/admin/${resourceName}/${item.id}`"
            :data="{ ...item as RequestPayload }"
            @success="editing = false"
          >
            <CheckIcon class="icon" title="Zapisz zmiany" />
          </RequestWrapper>
        </template>

        <button v-if="deletable" title="Usuń" @click="showDeleteMessage = true">
          <TrashIcon class="icon slide-up-animation text-red hover:text-red-500" />
        </button>
      </div>
    </div>

    <div class="w-full hidden md:block">
      <slot name="data" :item="item as T" :editing="editing" :errors="errors" />
    </div>

    <footer v-if="editing" class="flex flex-col justify-end text-right sm:flex-row gap-x-4">
      <span class="text-gray-400 text-xs"> Utworzony: {{ formatDate(item.createdAt) }}</span>
      <span class="text-gray-400 text-xs"> Ostatnio edytowany: {{ formatDate(item.updatedAt) }}</span>
    </footer>
  </div>
</template>
