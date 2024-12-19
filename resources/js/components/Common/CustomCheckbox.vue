<script setup lang="ts">
import { CheckIcon, MinusIcon } from '@heroicons/vue/24/outline'
import { ref, watch } from 'vue'

const props = defineProps<{ checked?: boolean, someChecked?: boolean }>()
const emit = defineEmits<{ check: [checked:boolean] }>()

const checkedRef = ref(props.checked || props.someChecked)

watch(() => props.checked || props.someChecked, isChecked => checkedRef.value=isChecked)
</script>

<template>
  <div class="flex items-center">
    <label class="relative flex items-center rounded-full cursor-pointer">
      <div
        class="before:content[''] relative size-5 cursor-pointer appearance-none rounded-md border-2 border-primary/30 transition-all before:absolute before:top-2/4 before:left-2/4 before:block before:size-12 before:-translate-y-2/4 before:-translate-x-2/4 before:rounded-full before:bg-blue-gray-500 before:opacity-0 before:transition-opacity hover:before:opacity-10"
        :class="{ 'border-primary bg-primary before:bg-primary': checkedRef }"
      >
        <input
          :checked="checkedRef"
          type="checkbox"
          class="sr-only"
          @change="checkedRef=!checkedRef; emit('check', checkedRef)"
        >

        <span
          class="absolute text-white transition-opacity opacity-0 pointer-events-none top-2/4 left-2/4 -translate-y-2/4 -translate-x-2/4"
          :class="{ 'opacity-100': checkedRef }"
        >
          <CheckIcon
            v-if="checked"
            class="size-3 stroke-4 flex-none rotate-6"
          />

          <MinusIcon
            v-else-if="someChecked"
            class="size-3 stroke-4 flex-none"
          />
        </span>
      </div>
    </label>
  </div>
</template>
