import { type Quiz } from '@/Types/Quiz'
import {type School} from '@/Types/School'

export interface Ranking {
  id: number
  name: string | null
  surname: string | null
  school: School
  points: number
}

export interface QuizRankingProps {
  quiz: Quiz
  rankings: Ranking[]
}
