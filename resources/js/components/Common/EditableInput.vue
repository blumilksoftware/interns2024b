<script setup lang="ts">
// onfocus -> is allowed to edit -> editing the content ref -> (onblur -> don't update the text) + | (oncheck -> update the text)
import { ref } from 'vue'
const isFocused = ref<boolean>(false)
const props = defineProps<{
  bold? :boolean
  type  :string
  icon? :boolean
  value?:string
}>()
const emit = defineEmits(['updateValue'])
const content = ref<string>(props.value ?? '')

function accept() {
  emit('updateValue', content.value)
}

function blur() {
  isFocused.value=false
  content.value = props.value ?? ''
}
</script>


<template>
  <label class="flex items-center justify-end bg-white/15backdrop-blur-md
    ring-1 ring-primary/30 rounded-lg cursor-pointer w-fit"
  >
    <input
      :type="type"
      :value="content"
      :class="{'font-bold':bold ? bold !== undefined : true, 'cursor-pointer' : !isFocused}"
      class="outline-none flex px-2 min-h-8 rounded-lg h-full focus:ring-2 focus:ring-primary/50 bg-white/50"
      autocomplete="off"
      @input="e=>content=(e.currentTarget as HTMLInputElement).value"
      @click.stop
      @focus="isFocused=true"
      @blur="blur"
    >
    <div v-if="!isFocused && icon" class="px-1 ">
      <svg 
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke-width="2"
        stroke="currentColor" 
        class="size-[1.3rem] -mt-1"
      >
        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
      </svg>
    </div>
    <button v-if="isFocused" class="p-2" @mousedown="accept">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5 text-primary">
        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
      </svg>
    </button>
  </label>
</template>

