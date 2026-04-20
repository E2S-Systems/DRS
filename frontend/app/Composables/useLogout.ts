export function useLogout() {
  const { logout } = useSanctumAuth()
  const toast = useToast()
  const loading = ref(false)

  async function logoutUser() {
    if (loading.value) return

    loading.value = true

    try {
      await logout()
    } catch (error) {
      toast.error({
        title: 'Erro!',
        message: 'Não foi possível sair no momento. Tente novamente.',
        timeout: 3000,
      })
    } finally {
      loading.value = false
    }
  }

  return { loading, logoutUser }
}