<script setup lang="ts">

import {ref} from 'vue'
import {calcSecondsLeftToDate, isTimeout, secondsToHour, timeToString} from '@/Helpers/Time'

const text = ref('')
const emit = defineEmits(['timeout'])
const props = defineProps<{ to?: string | number }>()

function updateTimer() {
  const timeLeft = secondsToHour(calcSecondsLeftToDate(props.to))

  text.value = timeToString(timeLeft, true)

  if (isTimeout(timeLeft))  {
    emit('timeout')
    return
  }

  setTimeout(updateTimer, 1000)
}
updateTimer()
</script>

<template>
  {{ text }}
</template>
