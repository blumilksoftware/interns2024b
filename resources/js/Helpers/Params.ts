export function useParams(): Record<string, string> {
  const searchParams = new URL(window.location.href).searchParams

  return Object.fromEntries(searchParams.entries())
}
