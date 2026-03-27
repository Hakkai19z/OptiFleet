import { useState, useEffect, useCallback } from 'react'
import { vehiculeService } from '../services/vehiculeService'
import { useToastStore } from '../store/toastStore'

export function useVehicules(params = {}) {
  const [vehicules, setVehicules] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState(null)
  const toast = useToastStore()

  const fetchVehicules = useCallback(async () => {
    setLoading(true)
    setError(null)
    try {
      const data = await vehiculeService.getAll(params)
      setVehicules(data['hydra:member'] ?? data)
    } catch (err) {
      setError(err)
      toast.error('Impossible de charger les véhicules')
    } finally {
      setLoading(false)
    }
  }, [JSON.stringify(params)])

  useEffect(() => {
    fetchVehicules()
  }, [fetchVehicules])

  const remove = async (id) => {
    await vehiculeService.remove(id)
    setVehicules((v) => v.filter((x) => x.id !== id))
    toast.success('Véhicule supprimé')
  }

  return { vehicules, loading, error, refetch: fetchVehicules, remove }
}
