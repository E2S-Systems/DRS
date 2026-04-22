export interface Role {
  code: string
  name: string
}

const roles: Role[] = [
  { code: 'admin',    name: 'Administrador' },
  { code: 'manager',  name: 'Gerente' },
  { code: 'employee', name: 'Colaborador' },
]

/**
 * @param code 
 * @returns
 */
export function getRoleLabel(code: string): string {
  return roles.find(role => role.code === code)?.name ?? code
}

export function useRoles() {
  return { roles, getRoleLabel }
}