import {type CleanAnswer} from '@/Types/CleanAnswer'

export interface CleanQuestion{
  temporaryId: string
  id?: number
  text: string
  answers: CleanAnswer[]
}
