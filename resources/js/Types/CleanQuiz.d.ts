import {type CleanQuestion} from '@/Types/CleanQuestion'

export interface CleanQuiz {
  key?: number
  name: string
  scheduledAt?: string
  duration?: number
  questions: CleanQuestion[]
}

