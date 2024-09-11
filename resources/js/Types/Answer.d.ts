import { type CleanAnswer } from '@/Types/CleanAnswer'

export interface Answer extends CleanAnswer{
  id: number
  createdAt: string
  updatedAt: string
  locked: boolean
}
