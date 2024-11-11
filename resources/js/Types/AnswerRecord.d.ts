interface PossibleRecordAnswer {
  id: number
  text: string
  correct?: boolean
}

interface AnswerRecord {
  id: number
  question: string
  createAt: string
  updatedAt: string
  closed: boolean
  selected?: number
  answers: PossibleRecordAnswer[]
}
