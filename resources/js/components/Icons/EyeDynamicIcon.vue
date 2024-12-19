<script setup lang="ts">
import { EyeIcon, EyeSlashIcon } from '@heroicons/vue/24/outline'

defineProps<{ opened: boolean }>()
</script>

<template>
  <EyeIcon
    v-if="opened"
    class="size-6 stroke-2"
  />

  <EyeSlashIcon
    v-else
    class="size-6 stroke-2"
  />
</template>
