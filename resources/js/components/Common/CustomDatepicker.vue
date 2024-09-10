<script setup lang="ts">
import Datepicker from '@vuepic/vue-datepicker'
import CalendarIcon from '../Icons/CalendarIcon.vue'

import { ref } from 'vue'
defineProps<{error?:string, isEditing:boolean, format: (content: any)=> string}>()
const model = defineModel<string | number>()
const datepickerRef = ref<InstanceType<typeof Datepicker>>()
</script>

<template>
  <div class="flex flex-col gap-1">
    <div class="w-full border border-primary/30 rounded-lg" :class="{'border-red': error}">
      <Datepicker
        ref="datepickerRef"
        v-model="model"
        locale="pl" 
        :format="format"
        :ui="{
          menu: 'datepicker-menu',
          input:'datepicker',
        }"
      >
        <template #input-icon>
          <CalendarIcon />
        </template>
        <template #action-buttons>
          <div class="flex gap-4">
            <button class="text-sm font-semibold rounded-md" @click="()=>datepickerRef?.toggleMenu()">Anuluj</button>
            <button class="bg-primary text-white text-sm font-bold py-2 px-3 rounded-md duration-200 hover:bg-primary-950" @click="()=>datepickerRef?.selectDate()">Wybierz</button>
          </div>
        </template>
      </Datepicker>
    </div>
    <span v-if="isEditing && error" :title="error" class="text-red text-sm truncate max-w-xs">{{ error }}</span>
  </div>
</template>
