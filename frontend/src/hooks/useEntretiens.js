import { useState, useEffect, useCallback } from 'react'
import { entretienService } from '../services/entretienService'
import { useToastStore } from '../store/toastStore'

export function useEntretiens(params = {}) {
  const [entretiens, setEntretiens] = useState([])
  const [loading, setLoading] = useState(true)
  const toast = useToastStore()

  const fetchEntretiens = useCallback(async () => {
    setLoading(true)
    try {
      const data = await entretienService.getAll(params)
      setEntretiens(data['hydra:member'] ?? data)
    } catch {
      toast.error('Impossible de charger les entretiens')
    } finally {
      setLoading(false)
    }
  }, [JSON.stringify(params)])

  useEffect(() => {
    fetchEntretiens()
  }, [fetchEntretiens])

  const remove = async (id) => {
    await entretienService.remove(id)
    setEntretiens((e) => e.filter((x) => x.id !== id))
    toast.success('Entretien supprimé')
  }

  return { entretiens, loading, refetch: fetchEntretiens, remove }
}
