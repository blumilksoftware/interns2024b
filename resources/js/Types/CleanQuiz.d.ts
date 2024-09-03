import {type CleanQuestion} from '@/Types/CleanQuestion'

export interface CleanQuiz {
  name: string
  scheduledAt?: number
  duration?: number
  questions: CleanQuestion[]
}

