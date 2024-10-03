<script setup lang="ts">
import { CheckCircleIcon } from '@heroicons/vue/20/solid'
import { XCircleIcon } from '@heroicons/vue/24/outline'

defineProps<{ correct:boolean }>()
</script>

<template>
  <CheckCircleIcon v-if="correct" />
  <XCircleIcon v-else />
</template>
