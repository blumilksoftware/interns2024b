import {onMounted, ref, type Ref, watch} from 'vue'
import dayjs from 'dayjs'
import {keysWrapper} from '@/Helpers/KeysManager'

function setSorter<T extends Sortable>(sorter: Ref<Sorter<T> | undefined>, sorterName: string, type: 'name' | 'title' | 'creationDate' | 'modificationDate', desc = false) {
  sessionStorage.setItem(`${sorterName}SorterPreference`, JSON.stringify([type, desc]))

  sorter.value = (a: T, b: T) => {
    if (desc) {
      [a, b] = [b, a]
    }

    const comparators = {
      title: () => ('title' in a && 'title' in b) ? a.title.localeCompare(b.title) : 0,
      name: () => ('name' in a && 'name' in b) ? a.name.localeCompare(b.name) : 0,
      creationDate: () => dayjs(a.createdAt).diff(dayjs(b.createdAt)),
      modificationDate: () => dayjs(a.updatedAt).diff(dayjs(b.updatedAt)),
    }

    return comparators[type]()
  }
}

export function useSorter<T extends Sortable>(sorterName: string, data: () => T[], options: SortOptionConstructor[]): [Ref<T[], T[]>, SortOption[]] {
  const items = ref<T[]>(data()) as Ref<T[]>
  const sorter = ref<(a: T, b: T) => number>()
  const sortOptions = keysWrapper(options.map((option) => ({
    text: option.text,
    action: () => setSorter(sorter, sorterName, option.type, option.desc) }
  )))

  onMounted(() => {
    const savedSorter = sessionStorage.getItem(`${sorterName}SorterPreference`)
    const [type, desc] = savedSorter ? JSON.parse(savedSorter) : ['modificationDate', true]
    setSorter(sorter, sorterName, type, desc)
  })

  watch(
    [data, sorter],
    ([newData, sorter]) => {
      items.value = newData
      items.value.sort(sorter)
    },
    { immediate: true },
  )

  return [items, sortOptions]
}
