export function groupBy<T>(key: keyof T, data: T[]): T[][] {
  const grouped = data.reduce((prev, current) => (
    {...prev, [current[key]]: [...(prev[current[key]] || []), current]}
  ), {})

  return Object.values(grouped).reverse()
}
