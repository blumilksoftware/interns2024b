import {type Ref} from 'vue'

export interface MessageBox {
  isOpen: Ref<boolean>
  close: () => void
  show: () => void
}
