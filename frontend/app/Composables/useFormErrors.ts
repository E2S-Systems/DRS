export function useFormErrors<T extends Record<string, string>>(fields: T) {
    const fieldErrors = reactive<Record<string, string>>({ ...fields })
    const apiError = ref<string | null>(null)

    function extractErrors(err: unknown): void {
        apiError.value = null
        Object.keys(fieldErrors).forEach(key => fieldErrors[key] = '')

        if (typeof err === 'object' && err !== null && 'data' in err) {
            const data = (err as { data: { message?: string; errors?: Record<string, string[]> } }).data

            if (data?.errors) {
                Object.entries(data.errors).forEach(([field, messages]) => {
                    if (field in fieldErrors) {
                        fieldErrors[field] = messages[0]
                    }
                })
                return
            }
            apiError.value = data?.message ?? 'Erro inesperado'
            return
        }
        apiError.value = 'Erro inesperado. Tente novamente.'
    }

    function resetErrors() {
        apiError.value = null
        Object.keys(fieldErrors).forEach(key => fieldErrors[key] = '')
    }

    return { fieldErrors, apiError, extractErrors, resetErrors }
}