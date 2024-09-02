import { type Quiz } from './Quiz'
import {type School} from '@/Types/School'

export interface Ranking {
  user_id: number
  user_name: string
  user_surname: string
  school: School
  points: number
}

export interface QuizRankingProps {
  quiz: Quiz
  rankings: Ranking[]
}
