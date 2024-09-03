import { router } from '@inertiajs/vue3'
import { ref } from 'vue'

export class Request{
  public isRequestOngoing = ref<boolean>(false)
  sendRequest(url : string, method: 'POST' | 'PATCH' | 'DELETE', payload? : Record<string,any>) {
    payload = {
      ...payload,
      preserveScroll: true,
      onStart: () => this.isRequestOngoing.value = true,
      onFinish: () =>  this.isRequestOngoing.value = false,
    }
    const inertiaRouterRequest = {
      'POST': () => payload = {...payload, method:'post'},
      'DELETE': () => payload = {...payload, method:'delete'},
      'PATCH': () => payload = {...payload, method:'post', _method:'PATCH'},
    }
    inertiaRouterRequest[method]()
    router.visit(url, payload)
  }
}
