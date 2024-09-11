<script setup lang="ts">
import {type Page} from '@/Types/Page'
import FormButton from '@/components/Common/FormButton.vue'
import { TransitionRoot, Dialog, TransitionChild, DialogPanel, DialogTitle } from '@headlessui/vue'
import { Bars3Icon, XMarkIcon } from '@heroicons/vue/24/outline'
import {ref} from 'vue'
import {type User} from '@/Types/User'

defineProps<{ pages: Page[], user?: User, appName: string }>()

const open = ref<boolean>(false)
const isSelected = (page: Page) => page.href === window.location.pathname
</script>

<template>
  <header class="w-full">
    <nav class="flex w-full mx-auto items-center justify-between gap-x-6 p-6 px-[8vw]">
      <div class="flex gap-x-12 items-center">
        <a href="/">
          <svg class="size-10" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M7.02484 14.3074C6.60832 14.3074 6.3744 13.828 6.63076 13.4997L15.3851 2.28913C15.7308 1.84642 16.4312 2.22227 16.2534 2.75511L13.6884 10.4434C13.6668 10.5082 13.715 10.575 13.7832 10.575H16.4951C16.9018 10.575 17.1384 11.0347 16.902 11.3656L9.30015 22.0091C8.96935 22.4723 8.24533 22.1111 8.41638 21.5683L10.6633 14.4375C10.6836 14.373 10.6355 14.3074 10.5679 14.3074H7.02484Z" fill="#262C89" />
            <path fill-rule="evenodd" clip-rule="evenodd" d="M12.2545 17.8224V6.2803L15.3847 2.28313C15.731 1.84101 16.4307 2.21751 16.2525 2.75005L13.6885 10.4136C13.6668 10.4784 13.715 10.5453 13.7834 10.5453H16.4935C16.9005 10.5453 17.137 11.0056 16.9 11.3365L12.2545 17.8224Z" fill="#6566C2" />
          </svg>
        </a>

        <a
          v-for="item in pages"
          :key="item.title"
          :href="item.href"
          class="hidden md:block font-semibold leading-6 text-gray-900 transition-colors duration-200 hover:text-primary-800"
          :class="{'text-primary': isSelected(item) }"
        >
          {{ item.title }}
        </a>
      </div>

      <div v-if="user" class="hidden md:flex flex-1 items-center justify-end gap-x-6">
        <FormButton method="post" href="/auth/logout" text>Wyloguj</FormButton>
      </div>

      <div v-if="pages.length > 0" class="flex md:hidden items-center">
        <div class="relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500" @click="open = true">
          <span class="absolute -inset-0.5" />
          <span class="sr-only">Otwórz menu główne</span>
          <Bars3Icon class="block size-6" />
        </div>
      </div>
    </nav>

    <TransitionRoot as="template" :show="open">
      <Dialog class="relative z-10" @close="open = false">
        <div class="fixed inset-0" />

        <div class="fixed inset-0 overflow-hidden">
          <div class="absolute inset-0 overflow-hidden">
            <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
              <TransitionChild as="template" enter="transform transition ease-in-out duration-500 sm:duration-700" enter-from="translate-x-full" enter-to="translate-x-0" leave="transform transition ease-in-out duration-500 sm:duration-700" leave-from="translate-x-0" leave-to="translate-x-full">
                <DialogPanel class="pointer-events-auto w-screen max-w-80">
                  <div class="flex h-full flex-col overflow-y-scroll bg-white py-6 shadow-xl">
                    <div class="px-4 sm:px-6">
                      <div class="flex items-start justify-between">
                        <DialogTitle class="text-base font-semibold leading-6 text-gray-900">{{ appName }}</DialogTitle>
                        <div class="ml-3 flex h-7 items-center">
                          <button type="button" class="relative rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2" @click="open = false">
                            <span class="absolute -inset-2.5" />
                            <span class="sr-only">Zamknij Menu</span>
                            <XMarkIcon class="size-6" aria-hidden="true" />
                          </button>
                        </div>
                      </div>
                    </div>
                    <div class="relative flex flex-col gap-4 mt-2 flex-1 px-6 py-2">
                      <a
                        v-for="item in pages"
                        :key="item.title"
                        :href="item.href"
                        class="block font-semibold leading-6 text-gray-900 mr-10"
                        :class="{'text-primary': isSelected(item) }"
                      >
                        {{ item.title }}
                      </a>
                    </div>

                    <div v-if="user" class="flex flex-1 items-end justify-center px-6 py-2">
                      <FormButton method="post" href="/auth/logout" text>Wyloguj</FormButton>
                    </div>
                  </div>
                </DialogPanel>
              </TransitionChild>
            </div>
          </div>
        </div>
      </Dialog>
    </TransitionRoot>
  </header>
</template>
