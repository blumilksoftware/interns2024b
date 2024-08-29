<script setup lang="ts">
import { XMarkIcon } from '@heroicons/vue/20/solid'
import { ref } from 'vue'
defineProps<{
  content?:string
}>()

const isVisible = ref<boolean>(true)
</script>

<template>
  <Transition>
    <div v-if="isVisible && !!content" class="flex absolute top-0 w-full items-center gap-x-6 bg-primary px-6 py-2.5 sm:px-3.5 sm:before:flex-1">
      <b class="text-white">
        {{ content }}
      </b>
      <div class="flex flex-1 justify-end">
        <button type="button" class="-m-3 p-3 focus-visible:outline-offset-[-4px]">
          <XMarkIcon class="size-5 text-white" aria-hidden="true" @click="isVisible=false" />
        </button>
      </div>
    </div>
  </Transition>
</template>
