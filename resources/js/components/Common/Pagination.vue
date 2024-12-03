<script setup lang="ts" generic="T">
import LinkButton from '@/components/Common/LinkButton.vue'

defineProps<{ data: Pagination<T>, query: string }>()
</script>

<template>
  <div class="p-2 flex items-center gap-1 justify-between">
    <LinkButton :disabled="!data.links.prev" :href="data.links.prev + (query ?? '')" icon>
      {{ '<' }}
    </LinkButton>

    <div class="text-nowrap text-center min-w-20">
      {{ data.meta.from ?? 0 }} - {{ data.meta.to ?? 0 }}
    </div>

    <LinkButton :disabled="!data.links.next" :href="data.links.next + (query ?? '')" icon>
      {{ '>' }}
    </LinkButton>
  </div>
</template>
