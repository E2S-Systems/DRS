export function useLoginForm() {
  const { login } = useSanctumAuth()
  const toast = useToast()
  const loading = ref(false)

  const form = ref({
    email: '',
    password: '',
    remember: true,
  })

  async function submit() {
    if (loading.value) return

    loading.value = true

    try {
      await login(form.value)
    } catch (error) {
      toast.error({
        title: 'Erro!',
        message: 'Credenciais inválidas. Tente novamente.',
        timeout: 3000,
      })
    } finally {
      loading.value = false
    }
  }

  return { form, loading, submit }
}