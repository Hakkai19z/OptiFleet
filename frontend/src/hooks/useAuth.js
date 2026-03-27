import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { authService } from '../services/authService'
import { useAuthStore } from '../store/authStore'
import { useToastStore } from '../store/toastStore'

export function useAuth() {
  const [loading, setLoading] = useState(false)
  const navigate = useNavigate()
  const { login, logout, token, user, isAdmin, isGestionnaire } = useAuthStore()
  const toast = useToastStore()

  const handleLogin = async (email, motDePasse) => {
    setLoading(true)
    try {
      const data = await authService.login(email, motDePasse)
      const me = await authService.me()
      login(data.token, me)
      toast.success('Connexion réussie', 'Bienvenue')
      navigate('/dashboard')
    } catch (err) {
      const msg = err.response?.status === 401
        ? 'Email ou mot de passe incorrect'
        : err.response?.status === 429
          ? 'Trop de tentatives. Réessayez dans 15 minutes.'
          : 'Erreur de connexion'
      toast.error(msg)
      throw err
    } finally {
      setLoading(false)
    }
  }

  const handleLogout = () => {
    logout()
    navigate('/login')
  }

  return {
    loading,
    token,
    user,
    isAdmin: isAdmin(),
    isGestionnaire: isGestionnaire(),
    login: handleLogin,
    logout: handleLogout,
  }
}
