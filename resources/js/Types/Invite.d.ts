import {type School} from '@/Types/School'

export interface Invite {
  id: number
  name: string | null
  surname: string | null
  school: School
}
