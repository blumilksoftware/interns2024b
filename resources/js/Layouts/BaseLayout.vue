<script setup lang="ts">

import type {Page} from '@/Types/Page'
import Header from '@/components/Common/Header.vue'
import Footer from '@/components/Common/Footer.vue'
import BackgroundEffect from '@/components/Common/BackgroundEffect.vue'
import Banner from '@/components/Common/Banner.vue'
import { usePage } from '@inertiajs/vue3'
import {ref} from 'vue'
import {type PageProps} from '@/Types/PageProps'

const { pages } = defineProps<{ pages: Page[] }>()

const props = usePage().props as PageProps
const status = ref<string>(props.status ?? '')

</script>

<template>
  <BackgroundEffect />

  <div class="flex flex-col items-center h-full min-h-screen">
    <Transition>
      <Banner v-if="status" :text="status" @click="status = ''" />
    </Transition>

    <Header :pages :user="props.user" :app-name="props.appName" />
    <slot />
    <Footer />
  </div>
</template>
