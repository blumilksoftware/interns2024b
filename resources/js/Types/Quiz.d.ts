import type Question from '@/Types/Question'

export default interface Quiz {
  [key: string]
  id: number
  title: string
  scheduledAt?: string
  duration?: number
  createdAt: string
  updatedAt: string
  state:'published' | 'locked' | 'unlocked'
  isUserAssigned: boolean
  isRankingPublished: boolean
  questions: Question[]
}
