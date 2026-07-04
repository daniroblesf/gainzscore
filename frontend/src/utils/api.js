export const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api'

export function getStoredUser() {
  const rawUser = localStorage.getItem('gainzscore_user')

  if (!rawUser) {
    return null
  }

  try {
    return JSON.parse(rawUser)
  } catch {
    localStorage.removeItem('gainzscore_user')
    return null
  }
}

export function storeSession({ token, user }) {
  localStorage.setItem('gainzscore_token', token)
  localStorage.setItem('gainzscore_user', JSON.stringify(user))
}

export function clearSession() {
  localStorage.removeItem('gainzscore_token')
  localStorage.removeItem('gainzscore_user')
}
