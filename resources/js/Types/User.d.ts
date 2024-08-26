import {type School} from '@/Types/School'

export interface User{
  id: number
  name: string
  surname: string
  email: string
  school: School
}
