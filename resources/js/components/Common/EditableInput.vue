<script setup lang="ts">
defineProps<{
  fontNormal? :boolean
  type  :string
  isEditing : boolean
  min?: string
}>()
const model = defineModel<string>()

</script>

<template>
  <span
    v-if="!isEditing"
    :class="{'font-normal':fontNormal }"
    class="py-1 font-bold"
  >
    {{ model ?? 'brak' }}
  </span>
  <input
    v-else
    v-model="model"
    :min="min"
    :type="type"
    :class="{'font-normal':fontNormal }"
    class="outline-none font-bold flex px-3 min-h-8 rounded-lg size-full ring-1 ring-primary/30 focus:ring-2 focus:ring-primary/50 bg-white/50"
    autocomplete="off"
  >
</template>
