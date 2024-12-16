<script lang="ts" setup>
import { ref, defineProps } from 'vue'
import { onClickOutside } from '@vueuse/core'
import vDynamicInputWidth from '@/Helpers/vDynamicInputWidth'

const target = ref()
onClickOutside(target, () => isFocused.value = false)

const isFocused = ref(false)

const props = defineProps<{
  value?: string
  error?: string
  editing: boolean
  label: string
  items: Array<{ key: string, text: string }>
}>()

const emit = defineEmits<{ change: [key: string] }>()
const selected = ref(props.items.find(item => item.key === props.value))

function onOptionClick(option: { key: string, text: string }) {
  selected.value = option
  isFocused.value = false
  emit('change', option.key)
}
</script>

<template>
  <div
    ref="target"
    class="placeholder:text-gray-400"
  >
    <div class="max-h-12 flex flex-col">
      <div class="flex h-inherit">
        <label
          for="school_id"
          class="pr-1"
        >
          {{ label }}
        </label>

        <input
          id="school_id"
          v-dynamic-input-width
          :value="selected?.text"
          class="text-md transition-none h-fit w-full outline-none font-bold border-b border-transparent bg-transparent focus:border-b-primary"
          :class="{
            'border-b-primary/30 hover:border-b-primary/60 text-primary duration-200 transition-colors' : editing,
            'border-b-red' : !!error,
            'cursor-pointer' : !isFocused && editing
          }"
          :disabled="!editing"
          @focus="isFocused=true"
        >
      </div>

      <span
        v-if="editing && error"
        :title="error"
        class="text-red text-sm truncate"
      >
        {{ error }}
      </span>
    </div>

    <div
      class=" max-h-12 flex flex-col duration-200"
      :class="{'scale-y-100 max-h-80': isFocused }"
    >
      <Transition>
        <div
          v-if="editing"
          v-show="isFocused"
          class="m-0.5 -mt-px py-2 overflow-auto border rounded-lg border-primary/60"
        >
          <div v-if="items.length > 0">
            <div
              v-for="item in items"
              :key="item.key"
              class="cursor-pointer block px-4 py-2 hover:bg-primary/10 focus:bg-primary/10 text-[0.9rem] w-full text-left"
              @click="onOptionClick(item)"
              @focus="isFocused=true"
            >
              <p>{{ item.text }}</p>
            </div>
          </div>
        </div>
      </Transition>
    </div>
  </div>
</template>
