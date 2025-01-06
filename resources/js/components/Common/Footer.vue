<script setup lang="ts">
import { ref } from 'vue'
import { BuildingOffice2Icon, EnvelopeIcon, PhoneIcon } from '@heroicons/vue/24/outline'
import ModalWindow from '@/components/Common/ModalWindow.vue'

const isContactVisible = ref(false)
</script>

<template>
  <footer class="bg-white/50 shadow w-full mt-auto">
    <div class="w-full mx-auto max-w-screen-xl p-4 md:flex md:items-center md:justify-between">
      <span class="text-sm text-gray-500 sm:text-center">
        © Collegium Witelona Uczelnia Państwowa
      </span>

      <ul class="flex flex-wrap items-center mt-3 text-sm font-medium text-gray-500 sm:mt-0">
        <li>
          <a
            href="/terms_and_conditions.pdf"
            target="_blank"
            class="hover:underline me-4 md:me-6"
          >
            Polityka prywatności
          </a>
        </li>

        <li>
          <p
            class="hover:underline cursor-pointer"
            @click="isContactVisible = true"
          >
            Kontakt
          </p>
        </li>
      </ul>
    </div>
  </footer>

  <ModalWindow
    v-model="isContactVisible"
    title="Kontakt"
  >
    <dl class="mt-2 space-y-4 text-base/7 text-gray-600">
      <div class="flex gap-x-4">
        <dt class="flex-none">
          <span class="sr-only">
            Adres
          </span>

          <BuildingOffice2Icon
            class="h-7 w-6 text-black"
            aria-hidden="true"
          />
        </dt>

        <dd>ul. Sejmowa 5A, 59-220 Legnica</dd>
      </div>

      <div class="flex gap-x-4">
        <dt class="flex-none">
          <span class="sr-only">
            Telefon
          </span>

          <PhoneIcon
            class="h-7 w-6 text-black"
            aria-hidden="true"
          />
        </dt>

        <dd>
          <a
            class="hover:text-primary"
            href="tel:76-723-21-50"
          >
            76-723-21-50
          </a>
        </dd>
      </div>

      <div class="flex gap-x-4">
        <dt class="flex-none">
          <span class="sr-only">
            Email
          </span>

          <EnvelopeIcon
            class="h-7 w-6 text-black"
            aria-hidden="true"
          />
        </dt>

        <dd>
          <a
            class="hover:text-primary"
            href="mailto:uczelnia@collegiumwitelona.pl"
          >
            uczelnia@collegiumwitelona.pl
          </a>
        </dd>
      </div>
    </dl>
  </ModalWindow>
</template>
