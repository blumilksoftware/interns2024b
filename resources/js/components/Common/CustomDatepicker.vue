<script setup lang="ts">
import { ref } from 'vue'
import { CalendarDaysIcon } from '@heroicons/vue/24/outline'
import Datepicker from '@vuepic/vue-datepicker'
import '@vuepic/vue-datepicker/dist/main.css'

defineProps<{
  error?:string
  format:(content:string) => string
}>()
const model = defineModel<string | number | Date>()
const datepickerRef = ref<InstanceType<typeof Datepicker>>()
</script>

<template>
  <div class="w-fit">
    <Datepicker
      ref="datepickerRef"
      v-model="model"
      locale="pl"
      :format="format"
      :min-date="new Date()"
      :ui="{
        menu: 'datepicker-menu',
        input: `datepicker ${error ? ' error' : ''}`,
      }"
    >
      <template #input-icon>
        <div class="h-6">
          <CalendarDaysIcon class="icon size-5" />
        </div>
      </template>
      <template #action-buttons>
        <div class="flex gap-4">
          <button class="text-sm font-semibold rounded-md" @click="() => datepickerRef?.toggleMenu()">Anuluj</button>
          <button
            class="bg-primary text-white text-sm font-bold py-2 px-3 rounded-md duration-200 hover:bg-primary-950"
            @click="() => datepickerRef?.selectDate()"
          >
            Wybierz
          </button>
        </div>
      </template>
    </Datepicker>
  </div>
</template>

<style>
:root {
  --dp-font-family: 'Poppins'
}

.datepicker {
  @apply rounded-none outline-none border-transparent border-b border-b-primary/30 font-bold p-0 pl-6 bg-transparent w-48 text-primary duration-200 transition-colors focus:border-b-primary/60
}

.dp__input:hover:not(.dp__input_focus) {
  @apply border-transparent border-b-primary/60
}

.dp__icon {
  @apply text-primary pb-2
}

.error {
  @apply border-b-red focus:border-b-red
}

.datepicker-menu {
  @apply rounded-lg border
}

.dp__active_date {
  @apply bg-primary font-bold
}

.dp__today {
  @apply border border-primary/30
}

.dp__btn {
  @apply fill-primary text-primary stroke-primary
}

.dp__btn:hover {
  @apply fill-primary-950 text-primary-950 stroke-primary-950
}

.dp__overlay {
  @apply rounded-lg
}
</style>
