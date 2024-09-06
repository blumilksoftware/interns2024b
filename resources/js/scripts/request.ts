import { type VisitPayload } from '@/Types/VisitPayload'
import { router } from '@inertiajs/vue3'
import { ref } from 'vue'

export class Request{
  public isRequestOngoing = ref<boolean>(false)
  public errors = ref<Record<string,string>>({})
  sendRequest(url : string, payload : VisitPayload) {
    payload = {
      ...payload,
      preserveScroll: true,
      preserveState: true,
      onStart: () => {this.errors.value = {}; this.isRequestOngoing.value = true},
      onFinish: () =>  this.isRequestOngoing.value = false,
      onError: (errors:Record<string,string>) => this.errors.value = errors,
    }
    router.visit(url, payload)
  }
}
