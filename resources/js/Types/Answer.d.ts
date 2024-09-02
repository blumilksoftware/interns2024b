import { type CleanAnswer } from '@/Types/CleanAnswer'

export interface Answer extends CleanAnswer{
  id: number
  createdAt: number
  updatedAt: number
  locked: boolean
}
