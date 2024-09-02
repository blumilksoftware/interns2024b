import {type AnswerRecord} from '@/Types/AnswerRecord'

export interface QuizSubmission {
  id: number
  name: string
  createAt: string
  updatedAt: string
  closedAt?: string
  closed: boolean
  quiz: number
  answers: AnswerRecord[]
}
