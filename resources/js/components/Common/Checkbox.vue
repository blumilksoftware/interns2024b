<script setup lang="ts">
defineProps<{ required?: boolean }>()
</script>

<template>
  <div class="inline-flex items-center">
    <label class="relative flex items-center rounded-full cursor-pointer">
      <input
        id="checkbox"
        type="checkbox"
        class="before:content[''] peer relative size-5 cursor-pointer appearance-none rounded-md border-2 border-primary/30 transition-all before:absolute before:top-2/4 before:left-2/4 before:block before:size-12 before:-translate-y-2/4 before:-translate-x-2/4 before:rounded-full before:bg-blue-gray-500 before:opacity-0 before:transition-opacity checked:border-primary checked:bg-primary checked:before:bg-primary hover:before:opacity-10"
        :required="required"
      >

      <span class="absolute text-white transition-opacity opacity-0 pointer-events-none top-2/4 left-2/4 -translate-y-2/4 -translate-x-2/4 peer-checked:opacity-100">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="size-3.5"
          viewBox="0 0 20 20"
          fill="currentColor"
          stroke="currentColor"
          stroke-width="1"
        >
          <path
            fill-rule="evenodd"
            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
            clip-rule="evenodd"
          />
        </svg>
      </span>
    </label>
  </div>
</template>
