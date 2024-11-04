import type { User } from '@/Types/User'

export interface Ranking {
  id: number
  user: User
  points: number
}
