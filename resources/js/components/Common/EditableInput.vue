<script setup lang="ts">
defineProps<{
  bold? :boolean
  type  :string
  isEditing : boolean
  min?: string
}>()
const model = defineModel<string>()

</script>

<template>
  <span
    v-if="!isEditing"
    :class="{'font-bold':bold ? bold !== undefined : true}"
    class="py-1"
  >
    {{ model ?? 'brak' }}
  </span>
  <input
    v-else
    v-model="model"
    :min="min"
    :type="type"
    :class="{'font-bold':bold ? bold !== undefined : true}"
    class="outline-none flex px-3 min-h-8 rounded-lg h-full ring-1 ring-primary/30 focus:ring-2 focus:ring-primary/50 bg-white/50"
    autocomplete="off"
  >
</template>
