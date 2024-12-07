export function groupBy<T>(key: keyof T, data: T[]): T[][] {
  const grouped = data.reduce<Record<string | number, T[]>>((prev, current) => (
    {...prev, [current[key] as string]: [...(prev[current[key] as string] || []), current]}
  ), {})

  return Object.values(grouped).reverse()
}
