import {type Answer} from '@/Types/Answer'

export interface Question {
  id: number
  text: string
  createdAt: number
  updatedAt: number
  locked: boolean
  correct?: number
  answers: Answer[]
}
