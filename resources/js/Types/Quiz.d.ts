import {type CleanQuiz} from '@/Types/CleanQuiz'

export interface Quiz extends CleanQuiz {
  id: number
  title: string
  createdAt: string
  updatedAt: string
  state:'published' | 'locked' | 'unlocked'
  isUserAssigned: boolean
  questions: Question[]
}
