import dayjs, { type ConfigType as DayjsConfigType } from 'dayjs'

export function formatDate(date?: DayjsConfigType, pretty=true): string {
  return dayjs(date).format(pretty ? 'DD.MM.YYYY HH:mm' : 'YYYY-MM-DD HH:mm:ss')
}
