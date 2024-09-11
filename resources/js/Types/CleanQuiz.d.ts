import {type CleanQuestion} from '@/Types/CleanQuestion'

export interface CleanQuiz {
  key: number | string
  title: string
  scheduledAt?: string
  duration?: number
  questions: CleanQuestion[]
}

