<script setup lang="ts">
import FormButton from '@/components/Common/FormButton.vue'
import { TransitionRoot, Dialog, TransitionChild, DialogPanel, DialogTitle } from '@headlessui/vue'
import { Bars3Icon, XMarkIcon } from '@heroicons/vue/24/outline'
import { ref } from 'vue'
import LogoIcon from '@/components/Icons/LogoIcon.vue'

defineProps<{ pages: Page[], user?: User, appName: string, title?: string }>()
const open = ref<boolean>(false)
const isSelected = (page: Page) => page.href === window.location.pathname
</script>

<template>
  <header class="w-full">
    <nav class="flex w-full items-center justify-between p-4">
      <div class="flex gap-x-6 items-center">
        <a href="/">
          <LogoIcon />
        </a>
        <span v-if="pages.length <= 0" class="font-semibold leading-6 text-gray-900">{{ title }}</span>
        <div v-else class="flex gap-6">
          <a
            v-for="item in pages"
            :key="item.title"
            :href="item.href"
            class="hidden sm:block font-semibold leading-6 text-gray-900 transition-colors duration-200 hover:text-primary-bright"
            :class="{ 'text-primary': isSelected(item) }"
          >
            {{ item.title }}
          </a>
        </div>
      </div>

      <FormButton
        v-if="user"
        class="hidden sm:block"
        method="post"
        href="/auth/logout"
        text
      >
        Wyloguj
      </FormButton>

      <div v-if="pages.length > 0" class="flex sm:hidden items-center">
        <div class="flex hover:cursor-pointer items-center justify-center rounded-md p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500" @click="open = true">
          <span class="sr-only">Otwórz menu główne</span>
          <Bars3Icon class="block size-6" />
        </div>
      </div>
    </nav>

    <TransitionRoot as="template" :show="open">
      <Dialog class="relative z-10" @close="open = false">
        <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
          <TransitionChild as="template" enter="transform transition ease-in-out duration-500 sm:duration-700" enter-from="translate-x-full" enter-to="translate-x-0" leave="transform transition ease-in-out duration-500 sm:duration-700" leave-from="translate-x-0" leave-to="translate-x-full">
            <DialogPanel class="pointer-events-auto w-screen max-w-80">
              <div class="flex h-full flex-col overflow-y-scroll bg-white py-6 shadow-xl">
                <div class="px-4 sm:px-6 flex items-start justify-between">
                  <DialogTitle class="text-base font-semibold leading-6 text-gray-900">{{ appName }}</DialogTitle>
                  <div class="ml-3 flex h-7 items-center">
                    <button type="button" class="relative rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2" @click="open = false">
                      <span class="sr-only">Zamknij Menu</span>
                      <XMarkIcon class="size-6" aria-hidden="true" />
                    </button>
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
      </Dialog>
    </TransitionRoot>
  </header>
</template>
