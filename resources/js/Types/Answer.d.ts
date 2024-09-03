import { type CleanAnswer } from '@/Types/CleanAnswer'

export interface Answer extends CleanAnswer{
  createdAt: number
  updatedAt: number
  locked: boolean
}
