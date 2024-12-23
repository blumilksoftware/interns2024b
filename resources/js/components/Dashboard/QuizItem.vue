<script setup lang="ts">
import dayjs from 'dayjs'

const props = defineProps<{
  title: string
  description?: string
  time?: string
}>()
</script>

<template>
  <div class="flex flex-col gap-5 2xs:flex-row 2xs:items-center rounded-xl bg-white/70 shadow border p-5 justify-between">
    <div class="flex flex-col gap-2">
      <p class="font-bold text-lg text-primary">
        {{ title }}
      </p>

      <p class="text-gray-600 text-sm">
        {{ description }}
      </p>

      <p class="text-gray-600 font-medium text-sm">
        {{ props.time ? dayjs(props.time).fromNow() : '' }}
      </p>
    </div>

    <div class="flex pr-1 justify-center w-full 2xs:w-fit ">
      <slot />
    </div>
  </div>
</template>
