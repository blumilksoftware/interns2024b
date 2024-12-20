import { type DirectiveBinding } from 'vue'

interface Props {
  _intersectionObserver: IntersectionObserver
}

const vIntersectionEvent = {
  mounted(el: HTMLElement & Props, binding: DirectiveBinding<() => void>) {
    const callback = binding.value
    el._intersectionObserver = new IntersectionObserver(
      ([{ isIntersecting }]) => isIntersecting && callback(),
    )
    el._intersectionObserver.observe(el)
  },
  unmounted(el: HTMLElement & Props) {
    el._intersectionObserver?.disconnect()
  },
}

export default vIntersectionEvent
