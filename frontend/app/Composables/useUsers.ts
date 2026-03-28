interface User {
  id: number
  first_name: string
  last_name: string
  email: string
  role: string
  is_active: boolean
  last_login_at: Date
}

export function useUsers() {
  const client = useSanctumClient()
  const { public: { apiUrl } } = useRuntimeConfig()

  const { data, pending, error, refresh } = useFetch<User[]>(`${apiUrl}/users`, {
    $fetch: client,
    default: () => [],
  })

  return { data, pending, error, refresh }
}