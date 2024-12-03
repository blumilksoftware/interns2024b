import dayjs, { type Dayjs } from 'dayjs'
import { usePlurals } from '@/Helpers/Plurals'

export function calcSecondsBetweenDates(from: string | number | Dayjs = 0, to: string | number | Dayjs = 0): number {
  return dayjs(from).diff(dayjs(to), 's')
}

export function calcSecondsLeftToDate(date: string | number | Dayjs = 0): number {
  return Math.max(calcSecondsBetweenDates(date, dayjs()), 0)
}

export function secondsToHour(seconds: number): TimeObject {
  return {
    'h': Math.floor(seconds / 3600),
    'm': Math.floor(seconds % 3600  / 60),
    's': seconds % 60,
  }
}

const translateLeft = usePlurals('Pozostała', 'Pozostały', 'Pozostało')
const translateSecondsLeft = usePlurals('sekunda', 'sekundy', 'sekund')
const translateMinutesLeft = usePlurals('minuta', 'minuty', 'minut')
const translateHoursLeft = usePlurals('godzina', 'godziny', 'godzin')

export function timeToString(time: TimeObject, withLeft = false): string {
  const { s, m, h } = time

  function formatTime(h:number|undefined, m:number|undefined, s:number|undefined) {
    const hours = h ? `${h} ${translateHoursLeft(h)}` : ''
    const minutes = m ? `${m} ${translateMinutesLeft(m)}` : ''
    const seconds =  s ? `${s} ${translateSecondsLeft(s)}` : ''
    const leading = h ?? m ?? s

    return `${(withLeft && leading ? translateLeft(leading) : '')} ${hours} ${minutes} ${seconds}`.trimStart()
  }

  if (h) {
    return formatTime(h,m,undefined)
  }

  if (m < 10) {
    return formatTime(undefined,m,s)
  }

  if (m) {
    return formatTime(undefined,m,undefined)
  }

  return formatTime(undefined,undefined,s)
}
