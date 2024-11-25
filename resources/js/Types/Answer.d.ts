interface Answer {
  [key: string]
  id?: number
  text: string
  correct: boolean
}

interface UserAnswer extends Answer {
  id: number
}
