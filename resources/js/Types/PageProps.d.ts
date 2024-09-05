import {type User} from '@/Types/User'

export interface PageProps {
  appName: string
  flash: {
    status?: string
  }
  errors?: Record<string, string>
  user?: User
}
