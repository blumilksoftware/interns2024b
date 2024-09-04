import {type CleanAnswer} from '@/Types/CleanAnswer'

export interface CleanQuestion{
  key: number | string
  text: string
  answers: CleanAnswer[]
}
