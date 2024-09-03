<script setup lang="ts">

import {ref} from 'vue'
import {polishPlurals} from 'polish-plurals'
import dayjs from 'dayjs'

const left = ref({'s': 0, 'm': 0, 'h': 0})
const emit = defineEmits(['timeout'])
const props = withDefaults(defineProps<{
    to?: string | number
  }>(), { to: 0 })

const withLeft = polishPlurals.bind(null, 'Pozostała', 'Pozostały', 'Pozostało')
const withSecond = polishPlurals.bind(null, 'sekunda', 'sekundy', 'sekund')
const withMinute = polishPlurals.bind(null, 'minuta', 'minuty', 'minut')
const withHour = polishPlurals.bind(null, 'godzina', 'godziny', 'godzin')

function updateTimer() {
  const seconds = Math.max(dayjs(props.to).diff(dayjs(), 's'), 0)

  left.value = {
    'h': Math.floor(seconds / 3600),
    'm': Math.floor(seconds % 3600  / 60),
    's': seconds % 60,
  }

  if (seconds <= 0)  {
    emit('timeout')
    return
  }

  setTimeout(updateTimer, 1000)
}
updateTimer()
</script>

<template>
  <div v-if="!left.m">{{ `${withLeft(left.s)} ${left.s} ${withSecond(left.s)}` }}</div>
  <div v-else-if="!left.h">{{ `${withLeft(left.m)} ${left.m} ${withMinute(left.m)}` }}</div>
  <div v-else>{{ `${withLeft(left.h)} ${left.h} ${withHour(left.h)} i ${left.m} ${withMinute(left.m)}` }}</div>
</template>
