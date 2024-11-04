import dayjs, { type Dayjs } from 'dayjs'
import { usePlurals } from '@/Helpers/Plurals'
import { type TimeObject } from '@/Types/TimeObject'

export function calcSecondsBetweenDates(from: string | number | Dayjs = 0, to: string | number | Dayjs = 0): number {
  return dayjs(from).diff(dayjs(to), 's')
}

export function calcSecondsLeftToDate(date: string | number | Dayjs = 0): number {
  return Math.max(calcSecondsBetweenDates(date, dayjs()), 0)
}

export function secondsToHour(seconds: number): TimeObject {
  return {
    h: Math.floor(seconds / 3600),
    m: Math.floor(seconds % 3600  / 60),
    s: seconds % 60,
  }
}

const translateLeft = usePlurals('Pozostała', 'Pozostały', 'Pozostało')
const translateSecondsLeft = usePlurals('sekunda', 'sekundy', 'sekund')
const translateMinutesLeft = usePlurals('minuta', 'minuty', 'minut')
const translateHoursLeft = usePlurals('godzina', 'godziny', 'godzin')

export function timeToString(time: TimeObject, withLeft = false): string {
  const { s, m, h } = time

  if (h <= 0 && m <= 0) {
    return `${withLeft ? translateLeft(s) : ''} ${s} ${translateSecondsLeft(s)}`.trimStart()
  }

  if (h <= 0 && m < 10 && s > 0) {
    return `${withLeft ? translateLeft(m) : ''} ${m} ${translateMinutesLeft(m)} i ${s} ${translateSecondsLeft(s)}`.trimStart()
  }

  if (h <= 0) {
    return `${withLeft ? translateLeft(m) : ''} ${m} ${translateMinutesLeft(m)}`.trimStart()
  }

  if (h <= 0 && m > 0) {
    return `${withLeft ? translateLeft(h) : ''} ${h} ${translateHoursLeft(h)} i ${m} ${translateMinutesLeft(m)}`.trimStart()
  }

  return `${withLeft ? translateLeft(h) : ''} ${h} ${translateHoursLeft(h)}`.trimStart()
}
