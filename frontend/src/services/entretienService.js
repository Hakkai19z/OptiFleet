import api from './api'

export const entretienService = {
  async getAll(params = {}) {
    const { data } = await api.get('/entretiens', { params })
    return data
  },

  async getById(id) {
    const { data } = await api.get(`/entretiens/${id}`)
    return data
  },

  async create(payload) {
    const { data } = await api.post('/entretiens', payload)
    return data
  },

  async update(id, payload) {
    const { data } = await api.put(`/entretiens/${id}`, payload)
    return data
  },

  async remove(id) {
    await api.delete(`/entretiens/${id}`)
  },
}
