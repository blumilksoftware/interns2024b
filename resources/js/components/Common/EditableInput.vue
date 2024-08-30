<script setup lang="ts">
defineProps<{
  bold? :boolean
  type  :string
  isEditing : boolean
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
  <label v-else class="flex items-center justify-end bg-white/15backdrop-blur-md
    ring-1 ring-primary/30 rounded-lg cursor-pointer w-fit"
  >
    <input
      v-model="model"
      :type="type"
      :class="{'font-bold':bold ? bold !== undefined : true}"
      class="outline-none flex px-3 min-h-8 rounded-lg h-full focus:ring-2 focus:ring-primary/50 bg-white/50"
      autocomplete="off"
    >
  </label>
</template>
