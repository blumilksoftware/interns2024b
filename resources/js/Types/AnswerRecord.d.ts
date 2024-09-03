export interface AnswerRecordAnswer {
  id: number
  text: string
}

export interface AnswerRecord {
  id: number
  question: string
  createAt: string
  updatedAt: string
  closed: boolean
  selected?: number
  answers: AnswerRecordAnswer[]
}
