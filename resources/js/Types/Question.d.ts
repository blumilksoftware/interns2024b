import type Answer from '@/Types/Answer'

export default interface Question {
  [key: string]
  id?: number
  text: string | undefined
  answers: Answer[]
}
