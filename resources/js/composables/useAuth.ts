import { usePage } from '@inertiajs/vue3'
export function useAuth() {
  const page = usePage()

  const permissions = page.props.auth.permissions;
  const roles = page.props.auth.roles;

  const can = (permission: string) => {
    return permissions.includes(permission) || roles.includes('Super Admin')
  }
  const hasRole = (role: string) => {
    return roles.includes(role) || roles.includes('Super Admin')
  }
  const canAny = (permissions: string[]) => {
    return permissions.some(permission => can(permission))
  }
  const canAll = (permissions: string[]) => {
    return permissions.every(permission => can(permission))
  } 

  return { can, hasRole, canAny, canAll }
}