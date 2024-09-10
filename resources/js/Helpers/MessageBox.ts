import {type MessageBox} from '@/Types/MessageBox'
import {ref} from 'vue'

export function useMessageBox(open = false): MessageBox {
  const isOpen = ref(open)

  return  {
    isOpen,
    show: () => isOpen.value = true,
    close: () => isOpen.value = false,
  }
}
