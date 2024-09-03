import {type CleanAnswer} from '@/Types/CleanAnswer'

export interface CleanQuestion{
  id: number
  text: string
  answers: CleanAnswer[]
}
