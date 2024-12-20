<script lang="ts" setup>
import { Dialog, DialogPanel, TransitionRoot, TransitionChild } from '@headlessui/vue'
import { type VNode } from 'vue'

defineSlots<{
  title: Node
  message: VNode
  buttons: VNode
}>()

defineProps<{ open: boolean }>()
const emit = defineEmits(['close'])
</script>

<template>
  <Teleport to="body">
    <TransitionRoot
      :show="open"
      as="template"
    >
      <Dialog
        class="relative z-50 duration-200"
        @close="emit('close')"
      >
        <TransitionChild
          as="template"
          enter="duration-200 ease-out"
          enter-from="opacity-0"
          enter-to="opacity-100"
          leave="duration-200 ease-in"
          leave-from="opacity-100"
          leave-to="opacity-0"
        >
          <div class="fixed inset-0 bg-black/25 backdrop-blur-md" />
        </TransitionChild>

        <div class="fixed inset-0 flex w-screen items-center justify-center p-4">
          <TransitionChild
            as="template"
            enter="duration-300 ease-out"
            enter-from="opacity-0 scale-95"
            enter-to="opacity-100 scale-100"
            leave="duration-200 ease-in"
            leave-from="opacity-100 scale-100"
            leave-to="opacity-0 scale-95"
          >
            <DialogPanel class="bg-white/85 shadow rounded-xl">
              <div class="px-4 py-5 sm:p-6">
                <h3 class="font-semibold leading-6 text-xl text-primary">
                  <slot name="title" />
                </h3>

                <div class="mt-2 max-w-xl text-black">
                  <slot name="message" />
                </div>

                <div class="mt-5 flex gap-4 justify-end">
                  <slot name="buttons" />
                </div>
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </Dialog>
    </TransitionRoot>
  </Teleport>
</template>
