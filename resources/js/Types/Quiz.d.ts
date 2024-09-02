import {type Question} from '@/Types/Question'

export interface Quiz {
  id: number
  name: string
  createdAt: number
  updatedAt: number
  scheduledAt?: number
  duration?: number
  state: 'unlocked' | 'locked' | 'published'
  questions: Question[]
}
