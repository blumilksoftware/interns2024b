import {computed, type Ref, ref, watch} from 'vue'
import {router} from '@inertiajs/vue3'
import {useParams} from '@/Helpers/Params'

export function useSorter(sortOptions: SortOption[], searchText?: Ref<string | undefined>, customQueries?: () => string[]): [query: Ref<string>, Ref<Option[]>] {
  const params = useParams()
  const key = ref(params.sort)
  const desc = ref(params.order)

  const options = computed(() => sortOptions.map<Option>((option) => ({
    ...option, action: () => { key.value = `${option.key}${desc.value ?? 'asc'}`; desc.value = option.desc ? 'desc' : 'asc' },
  })))

  const query = computed(() => {
    let query: string[] = []

    if (key.value && desc.value) {
      query.push(`sort=${key.value}`)
      query.push(`order=${desc.value}`)
    }

    if (searchText?.value) {
      query.push(`search=${searchText?.value}`)
    }

    if (customQueries) {
      query.push(...customQueries())
    }

    return query.length > 0 ? '&' + query.join('&') : ''
  })

  watch(query, query =>
    router.visit(
      `?page=1${query}`, {
        preserveState: true,
        replace: true,
      },
    ),
  )

  return [query, options]
}
