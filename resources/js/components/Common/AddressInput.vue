<script setup lang="ts">
import InputWrapper from '@/components/QuizzesPanel/InputWrapper.vue'
import {type Errors} from '@inertiajs/core'
import vDynamicInputWidth from '@/Helpers/vDynamicInputWidth'

const address = defineModel<Address>({ required: true })

defineProps<{
  errors: Errors
  disabled?: boolean
}>()
</script>

<template>
  <InputWrapper
    label="Ulica:"
    :has-content="!!address.street || !disabled"
    :error="errors.street"
    :show-error="!disabled"
  >
    <input
      v-model="address.street"
      v-dynamic-input-width
      type="text"
      name="street"
      autocomplete="off"
      :disabled="disabled"
      class="text-md transition-none h-fit w-full outline-none font-bold border-b border-transparent bg-transparent focus:border-b-primary"
      :class="{
        'border-b-primary/30 hover:border-b-primary/60 text-primary text-center' : !disabled,
        'border-b-red' : errors.street
      }"
    >
  </InputWrapper>

  <InputWrapper
    label="Numer budynku:"
    :has-content="!!value.buildingNumber || !disabled"
    :error="errors.building_number"
    :show-error="!disabled"
  >
    <input
      v-model="value.buildingNumber"
      v-dynamic-input-width
      type="text"
      name="buildingNumber"
      autocomplete="off"
      :disabled="disabled"
      class="text-md transition-none h-fit w-full outline-none font-bold border-b border-transparent bg-transparent focus:border-b-primary"
      :class="{
        'border-b-primary/30 hover:border-b-primary/60 text-primary text-center' : !disabled,
        'border-b-red' : errors.building_number
      }"
    >
  </InputWrapper>

  <InputWrapper
    label="Numer mieszkania:"
    :has-content="!!value.apartmentNumber || !disabled"
    :error="errors.apartment_number"
    :show-error="!disabled"
  >
    <input
      v-model="value.apartmentNumber"
      v-dynamic-input-width
      type="text"
      name="buildingNumber"
      autocomplete="off"
      :disabled="disabled"
      class="text-md transition-none h-fit w-full outline-none font-bold border-b border-transparent bg-transparent focus:border-b-primary"
      :class="{
        'border-b-primary/30 hover:border-b-primary/60 text-primary text-center' : !disabled,
        'border-b-red' : errors.apartment_number
      }"
    >
  </InputWrapper>

  <InputWrapper
    label="Kod pocztowy:"
    :has-content="!!value.zipCode || !disabled"
    :error="errors.zip_code"
    :show-error="!disabled"
  >
    <input
      v-model="value.zipCode"
      v-dynamic-input-width
      type="text"
      name="buildingNumber"
      autocomplete="off"
      :disabled="disabled"
      class="text-md transition-none h-fit w-full outline-none font-bold border-b border-transparent bg-transparent focus:border-b-primary"
      :class="{
        'border-b-primary/30 hover:border-b-primary/60 text-primary text-center' : !disabled,
        'border-b-red' : errors.zip_code
      }"
    >
  </InputWrapper>

  <InputWrapper
    label="Miasto:"
    :has-content="!!value.city || !disabled"
    :error="errors.city"
    :show-error="!disabled"
  >
    <input
      v-model="value.city"
      v-dynamic-input-width
      type="text"
      name="buildingNumber"
      autocomplete="off"
      :disabled="disabled"
      class="text-md transition-none h-fit w-full outline-none font-bold border-b border-transparent bg-transparent focus:border-b-primary"
      :class="{
        'border-b-primary/30 hover:border-b-primary/60 text-primary text-center' : !disabled,
        'border-b-red' : errors.city
      }"
    >
  </InputWrapper>
</template>
