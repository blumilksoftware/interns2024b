import { type AnswerRecord } from '@/Types/AnswerRecord'

export interface QuizSubmission {
  id: number
  name: string
  createdAt: string
  updatedAt: string
  closedAt?: string
  openedAt: string
  closed: boolean
  quiz: number
  answers: AnswerRecord[]
}
