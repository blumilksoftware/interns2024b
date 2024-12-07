interface User {
  id: number
  firstname: string
  surname: string
  email: string
  school: School
  school_id: number
  isAnonymized: boolean
  isAdmin: boolean
  isSuperAdmin: boolean
  createdAt: string
  updatedAt: string
}
