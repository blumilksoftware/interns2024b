import { onClickOutside } from '@vueuse/core'
import { type DirectiveBinding } from 'vue'


const vClickOutside = {
  mounted(el: HTMLElement, binding: DirectiveBinding<() => void>) {
    const callback = binding.value
    onClickOutside(el, callback)
  },
}
  
export default vClickOutside
