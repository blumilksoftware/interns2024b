import {type CleanQuestion} from '@/Types/CleanQuestion'

export interface CleanQuiz {
  id: number
  name: string
  scheduledAt?: number
  duration?: number
  questions: CleanQuestion[]
}

