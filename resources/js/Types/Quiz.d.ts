import {type CleanQuiz} from '@/Types/CleanQuiz'

export interface Quiz extends CleanQuiz {
  createdAt: number
  updatedAt: number
  state: 'unlocked' | 'locked' | 'published'
  questions: Question[]
}
