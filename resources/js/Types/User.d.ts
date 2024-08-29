import {type School} from '@/Types/School'

export interface User {
  id: number
  name: string
  surname: string
  email: string
  is_anonymized: boolean
  school: School
}
