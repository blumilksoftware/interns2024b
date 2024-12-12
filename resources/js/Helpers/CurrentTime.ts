import { ref, onMounted, onBeforeUnmount } from 'vue'

export default function useCurrentTime() {
  const currentTime = ref(Date.now())

  let updateTimeInterval: number

  onMounted(() => updateTimeInterval = setInterval(
    () => currentTime.value = Date.now(), 
    1000
    ,
  ))

  onBeforeUnmount(() => clearInterval(updateTimeInterval))

  return currentTime
}
