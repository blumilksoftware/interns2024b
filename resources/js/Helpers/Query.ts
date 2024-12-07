import qs from 'query-string'
import { computed, ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { useParams } from '@/Helpers/Params'

export function useQuery<T extends Record<string, any>>(defaultParams: T) {
  const queryParamsRef = ref({ ...defaultParams, ...useParams() })
  const queryString = computed(() => qs.stringify(queryParamsRef.value))
  
  watch(queryString, queryString => router.visit(
    `?${queryString}`, 
    {
      preserveState: true,
      replace: true,
    },
  ))
  
  return {
    queryParams: queryParamsRef.value as T,
    queryString: queryString.value,
  }
}
