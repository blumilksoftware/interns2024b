interface Quiz {
  [key: string]
  id: number
  title: string
  scheduledAt?: string
  duration?: number
  createdAt: string
  updatedAt: string
  state:'published' | 'locked' | 'unlocked'
  isLocal: boolean
  description?: string
  isUserAssigned: boolean
  isRankingPublished: boolean
  isPublic: boolean
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
