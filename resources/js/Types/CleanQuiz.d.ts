import {type CleanQuestion} from '@/Types/CleanQuestion'

export interface CleanQuiz {
  id: number
  name: string
  scheduledAt?: string
  duration?: number
  questions: CleanQuestion[]
}

