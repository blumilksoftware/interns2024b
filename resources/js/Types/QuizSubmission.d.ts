interface QuizSubmission {
  id: number
  title: string
  createdAt: string
  updatedAt: string
  closedAt?: string
  openedAt: string
  closed: boolean
  quiz: number
  answers: AnswerRecord[]
}
