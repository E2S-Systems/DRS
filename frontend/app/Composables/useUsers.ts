import type { User } from '~/types/user'

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

interface CreateUserDTO {
  first_name: string
  last_name: string
  email: string
  role: string
  password: string
  password_confirmation: string
  is_active: boolean
}

interface UpdateUserDTO {
  first_name?: string
  last_name?: string
  email?: string
  role?: string
  is_active?: boolean
  password?: string
}

export function useUsers() {
  const client = useSanctumClient()
  const { public: { apiUrl } } = useRuntimeConfig()

  const normalizedApiUrl = apiUrl.endsWith('/') ? apiUrl : `${apiUrl}/`

  const currentPage = useState('users.currentPage', () => 1)
  const search = useState('users.search', () => '')
  const status = useState<'' | boolean>('users.status', () => '')

  const isCreating = ref(false)
  const isUpdating = ref(false)
  const isDeleting = ref(false)

  const { data: response, pending, error, refresh } = useFetch<PaginatedResponse<User>>(
    `${normalizedApiUrl}users`,
    {
      $fetch: client,
      default: () => null,
      server: false,
      query: {
        page: currentPage,
        search: search,
        status: computed(() => status.value === '' ? undefined : status.value),
      },
    }
  )

  const data = computed(() => response.value?.data ?? [])
  const meta = computed(() => response.value?.meta ?? null)
  const counts = computed(() => response.value?.counts ?? { total: 0, active: 0, inactive: 0 })

  // --- Mutations ---

  async function createUser(payload: CreateUserDTO) {
    isCreating.value = true
    try {
      await client(`${normalizedApiUrl}users`, {
        method: 'POST',
        body: payload,
      })
      await refresh()
    } finally {
      isCreating.value = false
    }
  }

  async function updateUser(id: number, payload: UpdateUserDTO) {
    isUpdating.value = true
    try {
      await client(`${normalizedApiUrl}users/${id}`, {
        method: 'PUT',
        body: payload,
      })
      await refresh()
    } finally {
      isUpdating.value = false
    }
  }

  async function deleteUser(id: number) {
    isDeleting.value = true
    try {
      await client(`${normalizedApiUrl}users/${id}`, {
        method: 'DELETE',
      })

      if (data.value.length === 1 && currentPage.value > 1) {
        currentPage.value -= 1
      }

      await refresh()
    } finally {
      isDeleting.value = false
    }
  }

  // --- Filters / Pagination ---

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

  return {
    // list state
    data,
    meta,
    counts,
    pending,
    error,
    search,
    status,
    // mutation loadings
    isCreating,
    isUpdating,
    isDeleting,
    // actions
    refresh,
    createUser,
    updateUser,
    deleteUser,
    // filter handlers
    onPageChange,
    onSearch,
    onStatusChange,
  }
}