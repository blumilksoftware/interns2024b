import {type CleanQuiz} from '@/Types/CleanQuiz'

export interface Quiz extends CleanQuiz {
  id: number
  name: string
  createdAt: string
  updatedAt: string
  duration?: number
  state:'published' | 'locked' | 'unlocked'
  isUserAssigned: boolean
  questions: Question[]
}
