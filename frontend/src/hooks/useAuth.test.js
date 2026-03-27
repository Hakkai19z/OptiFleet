import { renderHook, act } from '@testing-library/react'
import { describe, it, expect, vi, beforeEach } from 'vitest'
import { MemoryRouter } from 'react-router-dom'
import { useAuth } from './useAuth'
import { useAuthStore } from '../store/authStore'

// Mock services
vi.mock('../services/authService', () => ({
  authService: {
    login: vi.fn(),
    me: vi.fn(),
  },
}))

vi.mock('../store/toastStore', () => ({
  useToastStore: () => ({
    success: vi.fn(),
    error: vi.fn(),
  }),
}))

vi.mock('react-router-dom', async () => {
  const actual = await vi.importActual('react-router-dom')
  return { ...actual, useNavigate: () => vi.fn() }
})

const wrapper = ({ children }) => <MemoryRouter>{children}</MemoryRouter>

describe('useAuth', () => {
  beforeEach(() => {
    useAuthStore.getState().logout()
    vi.clearAllMocks()
  })

  it('starts with loading false', () => {
    const { result } = renderHook(() => useAuth(), { wrapper })
    expect(result.current.loading).toBe(false)
  })

  it('starts unauthenticated', () => {
    const { result } = renderHook(() => useAuth(), { wrapper })
    expect(result.current.token).toBeNull()
    expect(result.current.user).toBeNull()
  })

  it('calls login service and sets token on success', async () => {
    const { authService } = await import('../services/authService')
    authService.login.mockResolvedValue({ token: 'test-jwt-token' })
    authService.me.mockResolvedValue({ email: 'admin@optifleet.fr', role: 'ROLE_ADMIN' })

    const { result } = renderHook(() => useAuth(), { wrapper })

    await act(async () => {
      await result.current.login('admin@optifleet.fr', 'Admin1234!')
    })

    expect(authService.login).toHaveBeenCalledWith('admin@optifleet.fr', 'Admin1234!')
  })

  it('throws on login failure', async () => {
    const { authService } = await import('../services/authService')
    authService.login.mockRejectedValue({ response: { status: 401 } })

    const { result } = renderHook(() => useAuth(), { wrapper })

    await expect(
      act(async () => {
        await result.current.login('admin@optifleet.fr', 'mauvais')
      })
    ).rejects.toBeDefined()
  })

  it('clears token on logout', async () => {
    useAuthStore.getState().login('some-token', { email: 'test@test.fr', role: 'ROLE_CONDUCTEUR' })

    const { result } = renderHook(() => useAuth(), { wrapper })

    act(() => {
      result.current.logout()
    })

    expect(useAuthStore.getState().token).toBeNull()
  })
})
