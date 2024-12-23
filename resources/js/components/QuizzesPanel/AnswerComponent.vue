<script setup lang="ts">
import CheckDynamicIcon from '@/components/Icons/CheckDynamicIcon.vue'
import vDynamicTextAreaHeight from '@/Helpers/vDynamicTextAreaHeight'
import { XMarkIcon } from '@heroicons/vue/24/outline'

defineProps<{ editing: boolean }>()
const answer = defineModel<Answer>({ required: true })
const emit = defineEmits<{ delete: [answer:Answer], setCorrect: [answer:Answer]}>()
</script>

<template>
  <label class="w-full flex gap-2">
    <button
      aria-label="Odznać jako prawidłowa odpowiedź"
      class="h-10"
      :disabled="!editing"
      @click="emit('setCorrect', answer)"
    >
      <CheckDynamicIcon
        class="icon stroke-1.5"
        :correct="answer.correct"
      />
    </button>

    <div class="flex items-center w-full">
      <span v-if="!editing">
        {{ answer.text }}
      </span>

      <textarea
        v-else
        v-model="answer.text"
        v-dynamic-text-area-height
        placeholder="Wpisz odpowiedź"
        class="h-12 w-full p-2 bg-transparent outline-none border-b border-primary/30 focus:border-primary/60"
      />
    </div>
    
    <button
      v-if="editing"
      title="Usuń odpowiedź"
      class="h-10"
      @click="emit('delete', answer)"
    >
      <XMarkIcon class="size-4 text-red stroke-3" />
    </button>
  </label>
</template>
