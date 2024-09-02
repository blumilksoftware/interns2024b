import {type CleanQuestion} from '@/Types/CleanQuestion'

export interface Question extends CleanQuestion{
  id: number
  createdAt: number
  updatedAt: number
  locked: boolean
  correct?: number
  answers: Answer[]
}
