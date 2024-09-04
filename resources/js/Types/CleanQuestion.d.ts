import {type CleanAnswer} from '@/Types/CleanAnswer'

export interface CleanQuestion{
  key: number
  text: string
  answers: CleanAnswer[]
}
