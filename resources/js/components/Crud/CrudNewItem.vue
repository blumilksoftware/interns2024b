<script setup lang="ts" generic="T">
import useRequestResolution from '@/Helpers/RequestResolution'
import RequestWrapper from '@/components/Common/RequestWrapper.vue'
import { CheckIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { vAutoAnimate } from '@formkit/auto-animate'
import {type Errors, type RequestPayload} from '@inertiajs/core'
import {ref} from 'vue'

const props = defineProps<{ resourceName: string, newItemData: T }>()
const emit = defineEmits(['done'])
const item = ref(props.newItemData)

defineSlots<{
  title: (scope: { item: T, editing: boolean, errors: Errors }) => any
  data: (scope: { item: T, editing: boolean, errors: Errors }) => any
}>()

const { processing, errors } = useRequestResolution()
</script>

<template>
  <div v-auto-animate class="flex flex-col gap-1 p-5 bg-white/70 border-2 rounded-xl shadow-sm">
    <Transition>
      <div v-show="processing" class="absolute bg-white/50 backdrop-blur-md z-10 size-full left-0 flex items-center justify-center -mt-4 rounded-xl">
        <div class="inline-block size-8 animate-spin rounded-full border-4 border-solid border-current border-e-transparent align-[-0.125em] text-primary motion-reduce:animate-[spin_1.5s_linear_infinite]" role="status" />
      </div>
    </Transition>

    <div class="flex justify-between">
      <div class="size-full md:h-auto px-0">
        <slot name="title" :item="item as T" :editing="true" :errors="errors" />

        <div class="h-full md:hidden pt-1">
          <slot name="data" :item="item as T" :editing="true" :errors="errors" />
        </div>
      </div>

      <div class="flex flex-col md:flex-row pl-5 gap-5 h-fit">
        <button
          title="Anuluj"
          @click="() => { errors={}; emit('done') }"
        >
          <XMarkIcon class="icon" />
        </button>

        <RequestWrapper
          title="Zapisz"
          method="post"
          :href="`/admin/${resourceName}`"
          :data="{ ...item as RequestPayload }"
          @success="emit('done')"
        >
          <CheckIcon class="icon" title="Zapisz" />
        </RequestWrapper>
      </div>
    </div>

    <div class="w-full hidden md:block">
      <slot name="data" :item="item as T" :editing="true" :errors="errors" />
    </div>
  </div>
</template>
