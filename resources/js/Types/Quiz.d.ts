import {type Question} from '@/Types/Question'

export interface Quiz extends CleanQuiz {
  id: number
  createdAt: number
  updatedAt: number
  state: 'unlocked' | 'locked' | 'published'
  questions: Question[]
}
