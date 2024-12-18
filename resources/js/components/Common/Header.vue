<script setup lang="ts">
import FormButton from '@/components/Common/FormButton.vue'
import { TransitionRoot, Dialog, TransitionChild, DialogPanel, DialogTitle } from '@headlessui/vue'
import { Bars3Icon, FlagIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { ref } from 'vue'
import LogoIcon from '@/components/Icons/LogoIcon.vue'
import Expand from './Expand.vue'

defineProps<{ pages: Page[], user?: User, appName: string, title?: string }>()
const open = ref<boolean>(false)
const isSelected = (page: Page) => page.href === window.location.pathname
</script>

<template>
  <header class="w-full z-30">
    <nav class="flex w-full items-center justify-between p-4">
      <div class="flex gap-x-6 items-center">
        <a href="/">
          <LogoIcon />
        </a>

        <span
          v-if="pages.length <= 0"
          class="font-semibold leading-6 text-gray-900"
        >
          {{ title }}
        </span>

        <div
          v-else
          class="flex gap-6"
        >
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

      <div
        v-if="pages.length > 0"
        class="flex sm:hidden items-center"
      >
        <div
          class="flex hover:cursor-pointer items-center justify-center rounded-md p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500"
          @click="open = true"
        >
          <span class="sr-only">
            Otwórz menu główne
          </span>

          <Bars3Icon class="block size-6" />
        </div>
      </div>
    </nav>

    <div
      class="bg-white h-full top-0 absolute left-full duration-200 flex flex-col gap-4 p-4 sm:hidden min-w-[50%] scale-95"
      :class="{'-translate-x-full shadow-lg !scale-100':open}"
    >
      <div class="flex justify-between option !py-3">
        <span class="font-bold">
          {{ appName }}
        </span>

        <button @click="open=false">
          <XMarkIcon class="size-5 stroke-2" />
        </button>
      </div>

      <span class="text-sm text-gray-400 px-3">
        Opcje
      </span>

      <a
        v-for="page in pages"
        :key="page.title"
        :href="page.href"
        class="option"
        :class="{'!text-primary': isSelected(page) }"
      >
        {{ page.title }}
      </a>

      <span class="text-sm text-gray-400 px-3 mt-auto">
        Sesja
      </span>

      <label class="option">
        <FormButton
          v-if="user"
          method="post"
          href="/auth/logout"
          text
        >
          Wyloguj
        </FormButton>
      </label>
    </div>
  </header>
</template>

<style scoped>
.option {
  @apply py-3 px-4 font-semibold leading-6 text-gray-900 bg-primary/[.02] rounded-xl
}
</style>
