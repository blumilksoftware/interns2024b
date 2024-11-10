<script setup lang="ts">
import {type Errors} from '@inertiajs/core'
import CrudInput from "@/components/Crud/CrudInput.vue";

const address = defineModel<Address>({ required: true })

defineProps<{
  errors: Errors
  disabled?: boolean
}>()
</script>

<template>
  <CrudInput
    name="street"
    label="Ulica:"
    :error="errors.street"
    :model-value="address.street"
    :editing="!disabled"
  />

  <CrudInput
    name="building_number"
    label="Numer budynku:"
    :error="errors.building_number"
    :model-value="address.buildingNumber"
    :editing="!disabled"
  />

  <CrudInput
    name="apartment_number"
    label="Numer lokalu:"
    :error="errors.apartment_number"
    :model-value="address.apartmentNumber"
    :editing="!disabled"
  />

  <CrudInput
    name="zip_code"
    label="Kod pocztowy:"
    :error="errors.zip_code"
    :model-value="address.zipCode"
    :editing="!disabled"
  />

  <CrudInput
    name="city"
    label="Miasto:"
    :error="errors.city"
    :model-value="address.city"
    :editing="!disabled"
  />
</template>
