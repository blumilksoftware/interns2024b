interface Quiz {
  [key: string]
  id: number
  title: string
  scheduledAt?: string
  duration?: number
  createdAt: string
  updatedAt: string
  state:'published' | 'locked' | 'unlocked'
  isUserAssigned: boolean
  isRankingPublished: boolean
  questions: Question[]
}

interface UserQuiz {
  [key: string]
  id?: number
  title: string
  closedAt?: string
  closed?: boolean
  quiz: number
  questions: UserQuestion[]
}
