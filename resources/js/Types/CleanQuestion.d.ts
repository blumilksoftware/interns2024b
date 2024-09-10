import {type CleanAnswer} from '@/Types/CleanAnswer'

export interface CleanQuestion{
  key: number | string
  text: string | undefined
  answers: CleanAnswer[]
}
