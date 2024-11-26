import { type PageProps as InertiaPageProps } from '@inertiajs/core'

export interface PageProps extends InertiaPageProps {
  appName: string
  flash: {
    status?: string
  }
  errors?: Record<string, string>
  user?: User
}
