import dayjs, { type ConfigType as DayjsConfigType } from 'dayjs'

export function formatDatePretty(date?:DayjsConfigType):string {
  return dayjs(date).format('DD.MM.YYYY HH:mm')
}
