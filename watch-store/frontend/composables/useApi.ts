import { useAuthStore } from '~/stores/auth'

export const useApi = () => {
  const config = useRuntimeConfig()
  const authStore = useAuthStore()

  const headers = () => {
    const h: Record<string, string> = {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
    }
    if (authStore.token) {
      h['Authorization'] = `Bearer ${authStore.token}`
    }
    return h
  }

  const handleResponse = async <T>(response: Response): Promise<T> => {
    if (!response.ok) {
      if (response.status === 401) {
        authStore.logout()
        if (import.meta.client) {
          navigateTo('/auth/login')
        }
      }
      const error = await response.json().catch(() => ({ message: 'Request failed' }))
      throw new Error(error.message || `HTTP ${response.status}`)
    }
    return response.json()
  }

  return {
    async get<T>(url: string, params?: Record<string, any>): Promise<T> {
      const query = params ? '?' + new URLSearchParams(params as any).toString() : ''
      const response = await fetch(`${config.public.apiBaseUrl}${url}${query}`, {
        method: 'GET',
        headers: headers(),
      })
      return handleResponse<T>(response)
    },
    async post<T>(url: string, body?: Record<string, any>): Promise<T> {
      const response = await fetch(`${config.public.apiBaseUrl}${url}`, {
        method: 'POST',
        headers: headers(),
        body: body ? JSON.stringify(body) : undefined,
      })
      return handleResponse<T>(response)
    },
    async put<T>(url: string, body?: Record<string, any>): Promise<T> {
      const response = await fetch(`${config.public.apiBaseUrl}${url}`, {
        method: 'PUT',
        headers: headers(),
        body: body ? JSON.stringify(body) : undefined,
      })
      return handleResponse<T>(response)
    },
    async delete<T>(url: string): Promise<T> {
      const response = await fetch(`${config.public.apiBaseUrl}${url}`, {
        method: 'DELETE',
        headers: headers(),
      })
      return handleResponse<T>(response)
    },
  }
}