<script lang="ts" setup>
import { ref, defineProps, computed} from 'vue'
import { onClickOutside } from '@vueuse/core'
import vDynamicInputWidth from '@/Helpers/vDynamicInputWidth'
import {type Errors} from '@inertiajs/core'

const target = ref()
onClickOutside(target, () => isFocused.value = false)

const isFocused = ref(false)
const searchQuery = ref('')

const props = defineProps<{
  value?: number
  errors: Errors
  editing: boolean
  schools:School[]
}>()

const emit = defineEmits<{ change: [value: number] }>()
const selected = ref<School | undefined>(props.schools.find(({ id }) => id === props.value))

const searchResult = computed(() =>
  props.schools.filter(school =>
    school.city.toLowerCase().includes(searchQuery.value) ||
    school.name?.toLowerCase().includes(searchQuery.value),
  ),
)

function onOptionClick(option: School) {
  selected.value = option
  isFocused.value = false
  emit('change', option.id)
}
</script>

<template>
  <div
    ref="target"
    class="overflow-hidden duration-200 max-h-12 flex flex-col placeholder:text-gray-400"
    :class="{'scale-y-100 max-h-80': isFocused }"
  >
    <div class="flex h-inherit duration-200">
      <label for="school_id" class="pr-1">Szkoła: </label>

      <input
        id="school_id"
        v-dynamic-input-width
        autocomplete="off"
        name="school_id"
        type="text"
        required
        :value="isFocused ? searchQuery : selected ? `${selected?.city} - ${selected?.name}` : ''"
        class="text-md transition-none h-fit w-full outline-none font-bold border-b border-transparent bg-transparent focus:border-b-primary"
        :class="{
          'border-b-primary/30 hover:border-b-primary/60 text-primary text-center' : editing,
          'border-b-red' : !!errors.school_id,
          'cursor-pointer' : !isFocused && editing
        }"
        :disabled="!editing"
        :placeholder="selected ? `${selected?.city} - ${selected?.name}` : ''"
        @input="(e:any)=>searchQuery=e.currentTarget.value"
        @focus="isFocused=true"
      >
    </div>

    <span v-if="editing && errors.school_id" :title="errors.school_id " class="text-red text-sm truncate">
      {{ errors.school_id }}
    </span>

    <Transition>
      <div v-if="editing" v-show="isFocused" class="m-0.5 -mt-px py-2 overflow-auto border rounded-lg border-primary/30">
        <div v-if="searchResult.length > 0">
          <div
            v-for="school in searchResult"
            :key="school.id"
            class="cursor-pointer block px-4 py-2 hover:bg-primary/10 focus:bg-primary/10 text-[0.9rem] w-full text-left"
            @click="onOptionClick(school)"
            @focus="isFocused=true"
          >
            <b v-if="school.city" class="text-gray-600">{{ school.city.toUpperCase() }}</b>
            <p>{{ school.name }}</p>
          </div>
        </div>
        <span v-else class="block px-4 py-2 text-sm">
          Nie znaleziono szkoły
        </span>
      </div>
    </Transition>
  </div>
</template>
