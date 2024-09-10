import axios from 'axios'
import { router, usePage } from '@inertiajs/vue3'
import { type VisitPayload } from '@/Types/VisitPayload'
import { ref } from 'vue'
const page = usePage()
export class Request{
  public isRequestOngoing = ref<boolean>(false)
  public errors = ref<Record<string,string>>({})
  sendRequest(url : string, payload : VisitPayload) {
    payload = {
      preserveScroll: true,
      preserveState: true,
      onStart: ()=>this.onStart(),
      onFinish: ()=>this.onFinish(),
      onError: (errors:Record<string,string>)=>this.onError(errors),
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
    if (errors)
      this.onError(errors)
    if (response && data.onSuccess)
      data.onSuccess()
    this.onFinish()
  }

  private async getResponse(promise: ()=>Promise<any>):Promise<[object?, Record<string,string>?]>{
    try { return [await promise(), undefined] }
    catch(errors : any){ return [undefined, errors] }
  }
  onStart(){
    this.errors.value = {}
    this.isRequestOngoing.value = true
  }
  private onError(errors:Record<string,string>){
    this.errors.value = errors
    console.log(errors)
  }
  private onFinish(){
    this.isRequestOngoing.value = false
  }
}
