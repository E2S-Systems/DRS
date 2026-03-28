interface PaginatedResponse<T> {
  data: T[]
  links: {
    first: string
    last: string
    prev: string | null
    next: string | null
  }
  meta: {
    current_page: number
    last_page: number
    per_page: number
    total: number
  }
  success: boolean
  message: string
}

interface User {
  id: number
  first_name: string
  last_name: string
  email: string
  role: string
  is_active: boolean
  last_login_at: string
}

export function useUsers() {
  const client = useSanctumClient()
  const { public: { apiUrl } } = useRuntimeConfig()

  const { data: response, pending, error, refresh } = useFetch<PaginatedResponse<User>>(
    `${apiUrl}users`,
    {
      $fetch: client,
      default: () => null,
    }
  )

  const data = computed(() => response.value?.data ?? [])

  return { data, pending, error, refresh }
}