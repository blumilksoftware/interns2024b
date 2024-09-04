import {type User} from '@/Types/User'

export interface PageProps {
  appName: string
  flash: {
    errors?: Record<string, string>
    status?: string
  }
  user?: User
}
