import type Answer from '@/Types/Answer'

export default interface Question {
  id?: number
  text: string | undefined
  answers: Answer[]
}
