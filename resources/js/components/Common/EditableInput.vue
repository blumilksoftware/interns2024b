<script setup lang="ts">
defineProps<{
  fontNormal?:boolean
  type:string
  isEditing:boolean
  min?:string
  error?:string
  format?:(content?:any ) => string
}>()
const model = defineModel<number | string>()
</script>

<template>
  <div
    v-if="!isEditing"
    :class="{'font-normal':fontNormal }"
    class="font-bold"
  >
    {{ format ? format(model) : model }}
  </div>
  <input
    v-else
    v-model="model"
    :min="min"
    :type="type"
    :class="{'font-normal':fontNormal, 'ring-red':error }"
    class="w-full outline-none font-bold px-3 rounded-lg ring-1 ring-primary/30 focus:ring-2 focus:ring-primary/50 bg-white/50"
    autocomplete="off"
  >
  <span v-if="isEditing && error" :title="error" class="text-red text-sm truncate max-w-xs">{{ error }}</span>
</template>
