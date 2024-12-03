<script setup lang="ts">
import { type Errors } from '@inertiajs/core'
import CrudInput from '@/components/Crud/CrudInput.vue'
import ZipCodeInput from '@/components/Common/ZipCodeInput.vue'
import InputWrapper from '@/components/QuizzesPanel/InputWrapper.vue'

const address = defineModel<Address>({ required: true })

defineProps<{
  errors: Errors
  disabled?: boolean
}>()
</script>

<template>
  <CrudInput
    v-model="address.street"
    name="street"
    label="Ulica:"
    :error="errors.street"
    :editing="!disabled"
  />

  <CrudInput
    v-model="address.buildingNumber"
    name="building_number"
    label="Numer budynku:"
    :error="errors.building_number"
    :editing="!disabled"
  />

  <CrudInput
    v-model="address.apartmentNumber"
    name="apartment_number"
    label="Numer lokalu:"
    :error="errors.apartment_number"
    :editing="!disabled"
  />

  <InputWrapper
    :error="errors.zip_code"
    :hide-error="disabled"
    :hide-content="!address.zipCode && disabled"
    label="Kod pocztowy:"
    :class="{ 'hidden': !address.zipCode && disabled }"
  >
    <ZipCodeInput
      v-model="address.zipCode"
      name="zip_code"
      :error="errors.zip_code"
      :editing="!disabled"
    />
  </InputWrapper>

  <CrudInput
    v-model="address.city"
    name="city"
    label="Miasto:"
    :error="errors.city"
    :editing="!disabled"
  />
</template>
