<script setup lang="ts">
import { ArchiveBoxXMarkIcon } from '@heroicons/vue/20/solid'
import { ArchiveBoxIcon } from '@heroicons/vue/24/outline'

defineProps<{ active: boolean }>()
</script>

<template>
  <ArchiveBoxIcon
    v-if="active"
    class="size-6"
  />

  <ArchiveBoxXMarkIcon
    v-else
    class="size-6"
  />
</template>
