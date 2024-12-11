import qs from 'query-string'
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { useParams } from '@/Helpers/Params'

export function useQuery<T extends Record<string, any>>(params: () => T) {
  const queryParamsRef = ref({ ...params(), ...useParams() })

  watch(
    params, 
    params => queryParamsRef.value = { ...queryParamsRef.value, ...params },
    { immediate: true },
  )
  
  watch(() => qs.stringify(queryParamsRef.value), queryString => router.visit(
    `?${queryString}`, 
    {
      preserveState: true,
      replace: true,
    },
  ))
  
  return queryParamsRef.value as T
}
