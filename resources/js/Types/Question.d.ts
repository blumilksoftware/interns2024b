interface Question {
  [key: string]
  text?: string
  answers: Answer[]
}

interface UserQuestion {
  [key: string]
  id: number
  text: string
  selectedAnswer?: number
  answers: UserAnswer[]
}
