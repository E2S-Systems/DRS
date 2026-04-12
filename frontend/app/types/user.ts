import type { PaginatedResponse } from "./api"

export interface User {
  id: number
  first_name: string
  last_name: string
  email: string
  role: string
  is_active: boolean
  last_login_at: string | null
}

export interface CreateUserDTO {
  first_name: string
  last_name: string
  email: string
  role: string
  password: string
  password_confirmation: string
  is_active: boolean
}

export interface UpdateUserDTO {
  first_name?: string
  last_name?: string
  email?: string
  role?: string
  password?: string | null
  password_confirmation?: string
}

export interface PaginatedUsersResponse<T> extends PaginatedResponse<T> {
  counts: {
    total: number
    active: number
  }
}