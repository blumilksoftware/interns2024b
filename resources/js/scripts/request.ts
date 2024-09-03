import { router } from '@inertiajs/vue3'
import { ref } from 'vue'

export class Request{
  public isRequestOngoing = ref<boolean>(false)
  public error = ref<string>('')
  sendRequest(url : string, method: 'POST' | 'PATCH' | 'DELETE', payload? : Record<string,any>) {
    payload = {
      ...payload,
      preserveScroll: true,
      preserveState: true,
      onStart: () => this.isRequestOngoing.value = true,
      onFinish: () =>  this.isRequestOngoing.value = false,
      onError: (error:{ name: string }) => this.error.value = error.name,
      method: method,
    }
    router.visit(url, payload)
  }
}
