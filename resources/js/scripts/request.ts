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
      method: method,
    }
    router.visit(url, payload)
  }
}
