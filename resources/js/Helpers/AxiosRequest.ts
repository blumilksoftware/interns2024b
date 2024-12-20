import axios, { type AxiosError } from 'axios'

interface AxiosResponse<T>{
	data: T|undefined
	error: AxiosError|undefined
}

interface AxiosRequestParams<T>{
  uri: string
  onStart?: () => void
  onPendingStateChange?: (isPending: boolean) => void
  onSuccess?: (data: T) => void
  onError?: (error: AxiosError) => void
}

export async function axiosRawRequest<T>(uri: string): Promise<AxiosResponse<T>>  {
  const response: AxiosResponse<T> = { data: undefined, error: undefined }
  
  try {
    response.data = (await axios.get(uri)).data
  }
  catch (error) {
    response.error = error as AxiosError
  }
  
  return response
}
export async function axiosRequest<T>({ uri, onStart, onPendingStateChange: setPendingState, onSuccess: setData, onError: setError }: AxiosRequestParams<T>) {
  onStart && onStart()
  
  setPendingState && setPendingState(true)

  const { data, error } = await axiosRawRequest<T>(uri)
  data && setData && setData(data)
  error && setError && setError(error)
    
  setPendingState && setPendingState(false)
}
