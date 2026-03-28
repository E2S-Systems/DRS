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
    from: number
    to: number
  }
  counts: {              
    total: number
    active: number
    inactive: number
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

  const currentPage = ref(1)
  const search = ref('')
  const status = ref<'' | boolean>('')

  const { data: response, pending, error, refresh } = useFetch<PaginatedResponse<User>>(
    `${apiUrl}users`,
    {
      $fetch: client,
      default: () => null,
      server: false,

      query: { page: currentPage, search: search, status: computed(() => status.value === '' ? undefined : status.value), },
    }
  )

  const data = computed(() => response.value?.data ?? [])
  const meta = computed(() => response.value?.meta ?? null)
  const counts = computed(() => response.value?.counts ?? { total: 0, active: 0, inactive: 0 })

  function onPageChange(event: { page: number }) {
    currentPage.value = event.page + 1
  }

  function onSearch(value: string) {
    search.value = value
    currentPage.value = 1
  }

  function onStatusChange(value: '' | boolean) {
    status.value = value
    currentPage.value = 1   
  }

  return { data, meta, pending, error, refresh, search, status, counts, onPageChange, onSearch, onStatusChange  }
}