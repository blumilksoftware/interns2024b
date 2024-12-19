<script setup lang="ts" generic="T">
import Button from '@/components/Common/Button.vue'

defineProps<{ from: number, to: number, disabledLeft?: boolean, disabledRight?: boolean }>()
const emit = defineEmits<{ switch: [isLeft:boolean]}>()
</script>

<template>
  <div class="p-2 flex items-center gap-1 justify-between">
    <Button
      :disabled="disabledLeft"
      icon
      @click="emit('switch', true)"
    >
      {{ '<' }}
    </Button>

    <div class="text-nowrap text-center min-w-20">
      {{ from }} - {{ to }}
    </div>

    <Button
      :disabled="disabledRight"
      icon
      @click="emit('switch', false)"
    >
      {{ '>' }}
    </Button>
  </div>
</template>
