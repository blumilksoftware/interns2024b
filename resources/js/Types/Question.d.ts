import {type CleanQuestion} from '@/Types/CleanQuestion'

export interface Question extends CleanQuestion{
  id: number
  createdAt: string
  updatedAt: string
  locked: boolean
  correct?: number
  answers: Answer[]
}
