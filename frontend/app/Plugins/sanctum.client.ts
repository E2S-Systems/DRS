export default defineNuxtPlugin(async () => {
  const { refreshIdentity } = useSanctumAuth()

  try {
    await refreshIdentity()
  } catch {
    // User is not authenticated — Sanctum will handle redirect via middleware
  }
})