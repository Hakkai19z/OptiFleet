import api from './api'

export const alerteService = {
  async getAll(params = {}) {
    const { data } = await api.get('/alertes', { params })
    return data
  },

  async getById(id) {
    const { data } = await api.get(`/alertes/${id}`)
    return data
  },

  async patch(id, payload) {
    const { data } = await api.patch(`/alertes/${id}`, payload, {
      headers: { 'Content-Type': 'application/merge-patch+json' },
    })
    return data
  },

  async resoudre(id) {
    return alerteService.patch(id, { statut: 'resolue' })
  },
}
