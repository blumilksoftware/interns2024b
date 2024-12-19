<script setup lang="ts">
withDefaults(
  defineProps<{
  disabled?: boolean
  checked?: boolean
  name?: string
  size?: number
}>(), 
  {
    name: undefined,
    size: 1,
  },
)
const emit = defineEmits<{ change: [] }>()
</script>

<template>
  <div>
    <input
      type="radio"
      :name="name"
      :disabled
      :checked
      :style="{ width: `${size}rem`, height: `${size}rem` }"
      @change.prevent="emit('change')"
    >
  </div>
</template>

<style>
input[type="radio"] {
  flex-shrink: 0;
  -webkit-appearance: none;
  appearance: none;
  background-color: transparent;
  font: inherit;
  color: currentColor;
  border-style: solid;
  border-color: currentColor;
  border-radius: 50%;
  display: grid;
  place-content: center;
  transition: 120ms border ease-in-out;
  border-width: .1rem;
}

input[type="radio"]:checked {
  border-width: .4rem;
}
</style>
