import { type Quiz } from './Quiz'
import { type User } from '@/Types/User'

export interface Ranking {
  user: User
  points: number
}

export interface QuizRankingProps {
  quiz: Quiz
  rankings: Ranking[]
}
