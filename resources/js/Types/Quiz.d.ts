import {type CleanQuiz} from '@/Types/CleanQuiz'

export interface Quiz extends CleanQuiz {
  createdAt: string
  updatedAt: string
  state: 'unlocked' | 'locked' | 'published'
  questions: Question[]
}
