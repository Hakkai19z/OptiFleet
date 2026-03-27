import api from './api'

export const affectationService = {
  async getAll(params = {}) {
    const { data } = await api.get('/affectations', { params })
    return data
  },

  async getById(id) {
    const { data } = await api.get(`/affectations/${id}`)
    return data
  },

  async create(payload) {
    const { data } = await api.post('/affectations', payload)
    return data
  },

  async terminer(id) {
    const { data } = await api.patch(`/affectations/terminer/${id}`)
    return data
  },

  async getActives() {
    const { data } = await api.get('/affectations/actives')
    return data
  },
}
