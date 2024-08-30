<script setup lang="ts">

withDefaults(defineProps< {
  label: string
  name: string
  error?: string
  type?: string
  required? : boolean
  placeholder? : string
  autocomplete?: string
}>(), {
  type: 'text',
  required: true,
  autocomplete: 'off',
  placeholder: '',
  error: '',
})

const model = defineModel<string>()
</script>

<template>
  <label class="block text-sm font-medium leading-6 text-gray-900">{{ label }}
    <div class="mt-2">
      <input
        v-model="model"
        :name="name"
        :placeholder="placeholder"
        :required="required"
        :type="type"
        :autocomplete="autocomplete"
        :class="{'ring-red focus:ring-red': error}"
        class="duration-200 ring-inset outline-none focus:ring focus:ring-primary bg-white/30 rounded-lg w-full p-3 text-gray-900 ring-2 ring-primary/30 placeholder:text-gray-400"
      >
      <div v-if="error" class="text-red">{{ error }}</div>
    </div>
  </label>
</template>
