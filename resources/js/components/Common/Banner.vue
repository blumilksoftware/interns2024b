<script setup lang="ts">
import { XMarkIcon } from '@heroicons/vue/20/solid'
import { watch } from 'vue'

const model = defineModel<string>()

function close() {
  model.value = ''
}

let timeoutId: ReturnType<typeof setTimeout>

watch(model, text => {
  clearTimeout(timeoutId)
  if (!text) return
  const time = text.split(' ').length * 1000
  timeoutId = setTimeout(close, time)
})
</script>

<template>
  <div 
    :class="{ '-translate-y-full': !model }"
    class="duration-200 text-white z-50 flex fixed top-0 w-full items-center gap-x-6 bg-primary px-6 py-2.5 sm:px-3.5 sm:before:flex-1"
  >
    <b> {{ model }} </b>
    <div class="flex flex-1 justify-end">
      <button type="button" class="-m-3 p-3 focus-visible:outline-offset-[-4px]">
        <XMarkIcon class="size-5" aria-hidden="true" @click="close" />
      </button>
    </div>
  </div>
</template>
