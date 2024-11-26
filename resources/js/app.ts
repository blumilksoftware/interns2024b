import '../css/app.css'
import { createApp, h, type DefineComponent } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { type Errors, type ErrorBag } from '@inertiajs/core'
import UserLayout from '@/Layouts/UserLayout.vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'
import 'dayjs/locale/pl'
import { type PageProps } from '@/Types/PageProps'

dayjs.extend(relativeTime)
dayjs.locale('pl')

const appName = import.meta.env.VITE_APP_NAME

createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: async (name) => {
    const page = await resolvePageComponent(
      `./Pages/${name}.vue`,
      import.meta.glob<DefineComponent>('./Pages/**/*.vue'),
    )

    if (name.startsWith('Admin/')) {
      page.default.layout ??= AdminLayout
    }

    if (name.startsWith('User/')) {
      page.default.layout ??= UserLayout
    }

    if (name.startsWith('Guest/')) {
      page.default.layout ??= GuestLayout
    }

    return page
  },
  setup({ el, App, props, plugin }) {
    const pageProps = props.initialPage.props as PageProps & { errors: Errors & ErrorBag }
    const themeClass = pageProps.user?.theme
    if (themeClass) {
      document.documentElement.classList.add(themeClass)
    }

    createApp({ render: () => h(App, props) })
      .use(plugin)
      .mount(el)
  },
  progress: {
    color: '#4B5563',
  },
})
