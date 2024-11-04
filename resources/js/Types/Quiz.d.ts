import { type Question } from '@/Types/Question'

export interface Quiz {
  id: number
  name: string
  createdAt: number
  updatedAt: number
  scheduledAt?: number
  duration?: number
  state: 'published' | 'locked' | 'unlocked'
  isUserAssigned: boolean
  isRankingPublished: boolean
  questions: Question[]
}
