import type { User, CreateUserDTO, UpdateUserDTO, PaginatedUsersResponse } from '~/types/user'
import { normalizeApiUrl } from '~/utils/api'

export function useUsers() {
  const client = useSanctumClient()
  const { public: { apiUrl } } = useRuntimeConfig()
  const normalizedApiUrl = normalizeApiUrl(apiUrl)
  const toast = useToast()

  const currentPage = useState('users.currentPage', () => 1)
  const search = useState('users.search', () => '')
  const status = useState<'' | boolean>('users.status', () => '')

  const isCreating = ref(false)
  const isUpdating = ref(false)
  const isDeleting = ref(false)

  const { data: response, pending, error, refresh } = useFetch<PaginatedUsersResponse<User>>(
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
      toast.success({
        title: 'Usuário criado com sucesso!',
        message: `Confirmação da criação de usuário`,
        icon: 'pi pi-check',
        position: 'topCenter',
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
      toast.success({
        title: 'Usuário atualizado com sucesso!',
        message: `Confirmação da atualização de usuário`,
        icon: 'pi pi-check',
        position: 'topCenter',
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

      toast.success({
        title: 'Usuário deletado com sucesso!',
        message: `Confirmação da exclusão de usuário`,
        icon: 'pi pi-check',
        position: 'topCenter',
      })

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