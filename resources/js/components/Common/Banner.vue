<script setup lang="ts">
import { XMarkIcon } from '@heroicons/vue/20/solid'
import { watch } from 'vue'

const emit = defineEmits(['close'])
const props = defineProps<{ show?: boolean, hideCloseButton?: boolean, message?:string }>()

let timeoutId: ReturnType<typeof setTimeout>

watch(props, props => {
  clearTimeout(timeoutId)

  if (!props.message) {
    return
  }

  const time = props.message.split(' ').length * 1000
  timeoutId = setTimeout(() => emit('close'), time)
})
</script>

<template>
  <div
    :class="{ '-translate-y-full': !show }"
    class="duration-200 text-white z-50 flex fixed top-0 w-full items-center gap-x-6 bg-primary px-6 py-2.5 sm:px-3.5 sm:before:flex-1 font-bold"
  >
    <p> {{ message }} </p>
    <div class="flex flex-1 justify-end">
      <button v-if="!hideCloseButton" type="button" class="-m-3 p-3 focus-visible:outline-offset-[-4px]">
        <XMarkIcon class="size-5" aria-hidden="true" @click="emit('close')" />
      </button>
    </div>
  </div>
</template>
