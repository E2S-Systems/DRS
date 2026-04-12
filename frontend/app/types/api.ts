export interface PaginatedResponse<T> {
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