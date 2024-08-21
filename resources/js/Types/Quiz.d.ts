import {type Question} from '@/Types/Question'

export interface Quiz {
  id: number
  scheduledAt?: number
  name: string
  createdAt: number
  updatedAt: number
  scheduledUntil?: number
  locked: boolean
  questions: Question[]
}
