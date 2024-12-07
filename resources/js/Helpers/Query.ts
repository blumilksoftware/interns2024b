import qs from 'query-string'
import { computed, ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { useParams } from '@/Helpers/Params'

export function useQuery<T extends Record<string, any>>(params: () => T) {
  const queryParamsRef = ref({ ...params(), ...useParams() })
  const queryString = computed(() => qs.stringify(queryParamsRef.value))

  watch(
    params, 
    params => queryParamsRef.value = { ...queryParamsRef.value, ...params },
    { immediate: true },
  )

  
  watch(queryString, queryString => router.visit(
    `?${queryString}`, 
    {
      preserveState: true,
      replace: true,
    },
  ))
  
  return queryParamsRef.value as T
}
