export interface User {
  id: number
  first_name: string
  last_name: string
  email: string
  role: string
  is_active: boolean
  last_login_at: string | null
}