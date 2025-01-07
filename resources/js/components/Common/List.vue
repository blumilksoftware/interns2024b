<script setup lang="ts">
import vScrollPreserver from '@/Helpers/vScrollPreserver'
import vIntersectionEvent from '@/Helpers/vIntersectionEvent'

defineProps<{
  options: Array<Option|any>
  opened: boolean
  useLazyLoading?: boolean
  fetching?: boolean
  noFetchText?: string
  noResultsText?: string
}>()

const emit = defineEmits<{ lazyload: [], optionClick: [option:Option|any] }>()
</script>


<template>
  <div
    class="z-10 flex flex-col overflow-hidden max-h-12 font-medium text-sm leading-6 text-gray-900 bg-white/30 placeholder:text-gray-400 rounded-lg ring-inset duration-200"
    :class="{ 'max-h-80 ring-2 ring-primary/30': opened }"
  >
    <slot />
    
    <Transition>
      <div
        v-if="opened"
        v-scroll-preserver
        class="m-0.5 mt-0 py-2 overflow-y-scroll relative"
      >
        <a
          v-for="option of options"
          :key="option.key"
          tabindex="0"
          class="block cursor-pointer px-4 py-2 hover:bg-primary/10 outline-none focus:bg-primary/10 text-[0.9rem] w-full text-left"
          @click="emit('optionClick', option)"
        >
          <b v-if="option.title">
            {{ option.title.toUpperCase() }}
          </b>

          <p>{{ option.text }}</p>
        </a>

        <div
          v-if="useLazyLoading"
          class="relative flex items-end -z-10"
        >
          <div
            v-intersection-event="() => emit('lazyload')"
            class="absolute h-64 w-full"
          />
        </div>

        <div
          v-if="fetching"
          class="bg-white/50 z-10 w-full h-fit left-0 flex items-center justify-center p-2"
        >
          <div
            class="inline-block size-8 animate-spin rounded-full border-4 border-solid border-current border-e-transparent align-[-0.125em] text-primary motion-reduce:animate-[spin_1.5s_linear_infinite]"
            role="status"
          />
        </div>

        <span
          v-else-if="options.length <= 0"
          class="block px-4 py-2 text-sm"
        >
          {{ noFetchText ? noFetchText : noResultsText }}
        </span>
      </div>
    </Transition>
  </div>
</template>
