import { useState, useEffect, useCallback } from 'react'
import { alerteService } from '../services/alerteService'
import { useToastStore } from '../store/toastStore'

export function useAlertes(params = {}) {
  const [alertes, setAlertes] = useState([])
  const [loading, setLoading] = useState(true)
  const toast = useToastStore()

  const fetchAlertes = useCallback(async () => {
    setLoading(true)
    try {
      const data = await alerteService.getAll(params)
      setAlertes(data['hydra:member'] ?? data)
    } catch {
      toast.error('Impossible de charger les alertes')
    } finally {
      setLoading(false)
    }
  }, [JSON.stringify(params)])

  useEffect(() => {
    fetchAlertes()
  }, [fetchAlertes])

  const resoudre = async (id) => {
    await alerteService.resoudre(id)
    setAlertes((a) => a.map((x) => x.id === id ? { ...x, statut: 'resolue' } : x))
    toast.success('Alerte résolue')
  }

  return { alertes, loading, refetch: fetchAlertes, resoudre }
}
