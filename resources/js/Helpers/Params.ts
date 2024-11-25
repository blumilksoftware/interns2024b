export function useParams(): Record<string, string | undefined> {
  const searchParams = new URL(window.location.href).searchParams
  let params:  Record<string, string> = {}

  for (const [key, value] of searchParams.entries()) {
    params[key] = value
  }

  return params
}
