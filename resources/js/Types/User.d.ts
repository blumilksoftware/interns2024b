interface User {
  id: number
  firstname: string
  surname: string
  email: string
  school: School
  isAnonymized: boolean
  forcePasswordChange: boolean
  isAdmin: boolean
  isSuperAdmin: boolean
  createdAt: string
  updatedAt: string
}
