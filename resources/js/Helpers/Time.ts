import {type TimeObject} from '@/Types/TimeObject'
import dayjs, { type Dayjs } from 'dayjs'
import {polishPlurals} from 'polish-plurals'

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

export function isTimeout(time: TimeObject): boolean {
  return time.s <= 0 && time.m <= 0 && time.h <= 0
}

const translateLeft = polishPlurals.bind(null, 'Pozostała', 'Pozostały', 'Pozostało')
const translateSecondsLeft = polishPlurals.bind(null, 'sekunda', 'sekundy', 'sekund')
const translateMinutesLeft = polishPlurals.bind(null, 'minuta', 'minuty', 'minut')
const translateHoursLeft = polishPlurals.bind(null, 'godzina', 'godziny', 'godzin')

export function timeToString(time: TimeObject, withLeft = false): string {
  const { s, m, h } = time

  if (h <= 0 && m <= 0) {
    return `${withLeft ? translateLeft(h) : ''} ${h} ${translateHoursLeft(h)} i ${m} ${translateMinutesLeft(m)}`.trimStart()
  }

  if (h <= 0 && m > 5) {
    return `${withLeft ? translateLeft(m) : ''} ${m} ${translateMinutesLeft(m)}`.trimStart()
  }

  if (m > 0) {
    return `${withLeft ? translateLeft(m) : ''} ${m} ${translateMinutesLeft(m)} i ${s} ${translateSecondsLeft(s)}`.trimStart()
  }

  return `${withLeft ? translateLeft(s) : ''} ${s} ${translateSecondsLeft(s)}`.trimStart()
}
