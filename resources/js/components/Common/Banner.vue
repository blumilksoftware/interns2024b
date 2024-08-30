<script setup lang="ts">
import { XMarkIcon } from '@heroicons/vue/20/solid'
defineProps<{text:string}>()
defineEmits<{ click: [] }>()
</script>

<template>
  <div class="flex absolute top-0 w-full items-center gap-x-6 bg-primary px-6 py-2.5 sm:px-3.5 sm:before:flex-1">
    <b class="text-white">
      {{ text }}
    </b>
    <div class="flex flex-1 justify-end">
      <button type="button" class="-m-3 p-3 focus-visible:outline-offset-[-4px]">
        <XMarkIcon class="size-5 text-white" aria-hidden="true" @click="$emit('click')" />
      </button>
    </div>
  </div>
</template>
