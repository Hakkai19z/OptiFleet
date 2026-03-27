import api from './api'

export const authService = {
  async login(email, motDePasse) {
    const { data } = await api.post('/auth/login', { email, motDePasse })
    return data
  },

  async me() {
    const { data } = await api.get('/auth/me')
    return data
  },
}
