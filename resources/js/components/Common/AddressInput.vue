<script setup lang="ts">
import {type Errors} from '@inertiajs/core'
import CrudInput from '@/components/Crud/CrudInput.vue'

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

  <CrudInput
    v-model="address.zipCode"
    name="zip_code"
    label="Kod pocztowy:"
    :error="errors.zip_code"
    :editing="!disabled"
  />

  <CrudInput
    v-model="address.city"
    name="city"
    label="Miasto:"
    :error="errors.city"
    :editing="!disabled"
  />
</template>
