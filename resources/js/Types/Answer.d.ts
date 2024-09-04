import { type CleanAnswer } from '@/Types/CleanAnswer'

export interface Answer extends Partial<CleanAnswer>{
  id: number
  createdAt: string
  updatedAt: string
  locked: boolean
}
