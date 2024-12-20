import { computed, ref } from 'vue'
import { calcSecondsLeftToDate, secondsToHour, timeToString } from '@/Helpers/Time'

export function useTimer(to: string | number | undefined, timeout: () => void) {
  const left = ref(calcSecondsLeftToDate(to))

  const interval = setInterval(
    () => {
      left.value = Math.max(0, left.value - 1)

      if (left.value === 0) {
        timeout()
        clearInterval(interval)
      }
    }, 
    1000,
  )

  return computed(() => timeToString(secondsToHour(left.value), true))
}
