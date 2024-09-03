import {type CleanQuiz} from '@/Types/CleanQuiz'

export interface Quiz extends CleanQuiz {
  id: number
  createdAt: number
  updatedAt: number
  state: 'unlocked' | 'locked' | 'published'
  questions: Question[]
}
