import { type VisitPayload } from '@/Types/VisitPayload'
import { router } from '@inertiajs/vue3'
import { ref } from 'vue'

export class Request{
  public isRequestOngoing = ref<boolean>(false)
  public error = ref<string>('')
  sendRequest(url : string, payload : VisitPayload) {
    payload = {
      ...payload,
      preserveScroll: true,
      preserveState: true,
      onStart: () => this.isRequestOngoing.value = true,
      onFinish: () =>  this.isRequestOngoing.value = false,
      onError: ({name}) => this.error.value = name,
    }
    router.visit(url, payload)
  }
}
