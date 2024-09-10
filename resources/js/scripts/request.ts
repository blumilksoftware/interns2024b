import axios, {type AxiosError} from 'axios'
import { router, usePage } from '@inertiajs/vue3'
import { type VisitPayload } from '@/Types/VisitPayload'
import { ref } from 'vue'

const page = usePage()
export class Request{
  public isRequestOngoing = ref<boolean>(false)
  public errors = ref<Record<string, string>>()
  sendRequest(url : string, payload : VisitPayload) {
    payload = {
      preserveScroll: true,
      preserveState: true,
      onStart: ()=>this.onStart(),
      onFinish: ()=>this.onFinish(),
      onError: (err:Record<string,string>)=>this.onError(err),
      ...payload,
    }
    router.visit(url, payload)
  }

  async axiosPatch(url:string, data:Record<string, any>){
    this.onStart()
    const promiseCallback = async ()=>await axios.post(
      url,
      { ...data, _method: 'PATCH' },
      { headers: { 'X-CSRF-TOKEN': page.props.csrf_token as string } },
    )
    const [response, errors] = await this.getResponse(promiseCallback)
    if (errors){
      this.onError(errors)
    }
    if (response && data.onSuccess)
      data.onSuccess()
    this.onFinish()
  }

  private async getResponse(promiseCallback: ()=>Promise<any>) : Promise<[any?, Record<string, string>?]> {
    try { 
      return [await promiseCallback(), undefined]
    }
    catch(err : unknown){
      const unknownError = [undefined, {unknown: 'Niewiadomy błąd się pojawił'}] as [undefined, Record<string, string>]
      if (!axios.isAxiosError(err)) return unknownError 
      const axiosError = err as AxiosError<Record<string, Record<string, string[]>>>
      if (!axiosError.response) return unknownError
      const errors : Record<string, string[]> = axiosError.response.data.errors
      const uninfiedErrors: Record<string, string> = Object.fromEntries(
        Object.entries(errors).map(([key, value]) => [key, value.join(' ')]),
      )
      return [ undefined, uninfiedErrors ]
    }
  }
  onStart(){
    this.errors.value = {}
    this.isRequestOngoing.value = true
  }
  private onError(errors: Record<string, string>){
    this.errors.value = errors
    console.log(errors)
  }
  private onFinish(){
    this.isRequestOngoing.value = false
  }
}
