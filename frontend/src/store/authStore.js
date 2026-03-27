import { create } from 'zustand'
import { persist } from 'zustand/middleware'

export const useAuthStore = create(
  persist(
    (set, get) => ({
      token: null,
      user: null,

      setToken: (token) => set({ token }),
      setUser: (user) => set({ user }),

      login: (token, user) => set({ token, user }),

      logout: () => {
        set({ token: null, user: null })
        localStorage.removeItem('optifleet-auth')
      },

      isAuthenticated: () => !!get().token,

      hasRole: (role) => {
        const user = get().user
        if (!user) return false
        const hierarchy = {
          ROLE_ADMIN: ['ROLE_ADMIN', 'ROLE_GESTIONNAIRE', 'ROLE_CONDUCTEUR'],
          ROLE_GESTIONNAIRE: ['ROLE_GESTIONNAIRE', 'ROLE_CONDUCTEUR'],
          ROLE_CONDUCTEUR: ['ROLE_CONDUCTEUR'],
        }
        return (hierarchy[user.role] || []).includes(role)
      },

      isAdmin: () => get().user?.role === 'ROLE_ADMIN',
      isGestionnaire: () => ['ROLE_ADMIN', 'ROLE_GESTIONNAIRE'].includes(get().user?.role),
    }),
    {
      name: 'optifleet-auth',
      partialize: (state) => ({ token: state.token, user: state.user }),
    }
  )
)
