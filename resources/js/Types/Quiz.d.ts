import {type Question} from '@/Types/Question'

export interface Quiz {
  id: number
  name: string
  createdAt: number
  updatedAt: number
  locked: boolean
  questions: Question[]
}
