<script setup lang="ts" generic="T">
import InputWrapper from '@/components/QuizzesPanel/InputWrapper.vue'
import vDynamicInputWidth from '@/Helpers/vDynamicInputWidth'
import { nextTick } from 'vue'
import { type InputProps } from '@/Types/InputProps'
  
const value = defineModel<T>({ required: true })
  
const props = defineProps<{
    editing?: boolean
    selected?: boolean
    label?: string
    error?: string
    password?: boolean
    large?: boolean
    format?: (value: T) => any
  } & InputProps>()

function handleInput() {
  nextTick(() => {
    if (props.format) {
      value.value = props.format(value.value)
    }
  })
}
</script>

<template>
  <div
    class="flex gap-1 duration-200 min-h-7"
    :class="{ 'text-sm text-gray-600' : !selected && !editing && !large, 'text-lg h-8': large }"
  >
    <InputWrapper
      :label
      :hide-content="!value && !editing"
      :error="error"
      :hide-error="!editing"
    >
      <div class="grid flex-1">
        <Transition name="no-transition">
          <b
            v-if="!editing"
            class="row-start-1 col-start-1"
          >
            {{ format ? format(value) : value }}
          </b>

          <div
            v-else
            class="size-full row-start-1 col-start-1"
          >
            <slot>
              <input
                v-model="value"
                v-dynamic-input-width
                :type="type ?? 'text'"
                :name
                :min
                :autocomplete="autocomplete ?? 'off'"
                class="text-md transition-colors h-fit outline-none font-bold border-b border-transparent bg-transparent focus:border-b-primary"
                :class="{
                  'border-b-primary/30 hover:border-b-primary/60 text-primary text-center': editing,
                  'border-b-red': error,
                  '!text-lg': large
                }"
                @input="handleInput"
              >
            </slot>
          </div>
        </Transition>
      </div>
    </InputWrapper>
  </div>
</template>
