import { axiosRequest } from '@/Helpers/AxiosRequest'
import qs from 'query-string'

export async function schoolsFetcher(schools: Pagination<School>, search?: string): Promise<[Pagination<School>, boolean, string]> {
  let pending=false
  let error=''
  
  const page = schools.meta?.current_page ?? 0
  
  const paramsString = qs.stringify({
    search: search,
    page: search ? undefined : page + 1,
  })
  
  await axiosRequest<Pagination<School>>({
    uri: `/schools/search?${paramsString}`,
    onPendingStateChange: isPending => pending = isPending,
    onError: () => error = 'Nie udało się pobrać więcej szkół',
    onSuccess: pagination => schools = {
      ...pagination,
      data: [
        ...schools.data, 
        ...(!search ? pagination.data : pagination.data.filter(
          x => !schools.data.some(y => y.id === x.id),
        )),
      ], 
    },
  })
  
  return [schools, pending, error]
}
