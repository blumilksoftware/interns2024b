<script lang="ts" setup>
import {Dialog, DialogPanel} from '@headlessui/vue'
import {type MessageBox as MessageBoxProps} from '@/Types/MessageBox'
import {type VNode} from 'vue'

defineSlots<{
  title: Node
  message: VNode
  buttons: VNode
}>()

defineProps<MessageBoxProps>()
</script>

<template>
  <Dialog :open="isOpen.value" class="relative z-50" @close="close">
    <div class="fixed inset-0 bg-black/30" aria-hidden="true" />

    <div class="fixed inset-0 flex w-screen items-center justify-center p-4">
      <DialogPanel class="bg-white shadow rounded-lg">
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
    </div>
  </Dialog>
</template>
