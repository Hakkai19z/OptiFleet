import api from './api'

export const vehiculeService = {
  async getAll(params = {}) {
    const { data } = await api.get('/vehicules', { params })
    return data
  },

  async getById(id) {
    const { data } = await api.get(`/vehicules/${id}`)
    return data
  },

  async create(payload) {
    const { data } = await api.post('/vehicules', payload)
    return data
  },

  async update(id, payload) {
    const { data } = await api.put(`/vehicules/${id}`, payload)
    return data
  },

  async patch(id, payload) {
    const { data } = await api.patch(`/vehicules/${id}`, payload, {
      headers: { 'Content-Type': 'application/merge-patch+json' },
    })
    return data
  },

  async remove(id) {
    await api.delete(`/vehicules/${id}`)
  },
}
