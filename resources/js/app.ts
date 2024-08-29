import '../css/app.css'
import { createApp, h, type DefineComponent } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import UserLayout from '@/Layouts/UserLayout.vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

createInertiaApp({
  title: (title) => `${title}`,
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

    console.log(name)

    return page
  },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .mount(el)
  },
  progress: {
    color: '#4B5563',
  },
})
