import {type Answer} from '@/Types/Answer'

export interface Question extends CleanQuestion{
  id: number
  createdAt: number
  updatedAt: number
  locked: boolean
  correct?: number
  answers: Answer[]
}
