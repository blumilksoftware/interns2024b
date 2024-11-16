<script setup lang="ts">
withDefaults(defineProps<{
  disabled?: boolean
  checked?: boolean
  name?: string
  size?: number
}>(), {
  name: undefined,
  size: 1,
})
const emit = defineEmits<{ change: [] }>()
</script>

<template>
  <div>
    <input
      type="radio"
      :name="name"
      :disabled
      :checked
      :style="{ width: `${size}rem`, height: `${size}rem`, borderWidth: `${size/10}rem`, '--inner-size': `calc(${size}rem*.6)` }"
      @change.prevent="emit('change')"
    >
  </div>
</template>

<style>
input[type="radio"] {
  flex-shrink: 0;
  -webkit-appearance: none;
  appearance: none;
  background-color: #fff;
  margin: 0;
  font: inherit;
  color: currentColor;
  border-style: solid;
  border-color: currentColor;
  border-radius: 50%;
  transform: translateY(-0.075rem);
  display: grid;
  place-content: center;
}

input[type="radio"]::before {
  content: "";
  width: var(--inner-size);
  height: var(--inner-size);
  border-radius: 50%;
  transform: scale(0);
  transition: 120ms transform ease-in-out;
  box-shadow: inset var(--inner-size) var(--inner-size) currentColor;
}

input[type="radio"]:checked::before {
  transform: scale(1);
}
</style>
