import {type CleanQuestion} from '@/Types/CleanQuestion'

export interface CleanQuiz {
  key: number | string
  name: string
  scheduledAt?: string
  duration?: number
  questions: CleanQuestion[]
}

