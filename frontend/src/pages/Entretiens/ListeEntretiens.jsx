import { useState, useEffect } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import Table from '../../components/ui/Table'
import Badge from '../../components/ui/Badge'
import Button from '../../components/ui/Button'
import { entretienService } from '../../services/entretienService'
import { useToastStore } from '../../store/toastStore'
import { useAuthStore } from '../../store/authStore'

export default function ListeEntretiens() {
  const [entretiens, setEntretiens] = useState([])
  const [loading, setLoading] = useState(true)
  const navigate = useNavigate()
  const toast = useToastStore()
  const { isGestionnaire } = useAuthStore.getState()

  useEffect(() => {
    entretienService.getAll()
      .then((data) => setEntretiens(data['hydra:member'] ?? data))
      .catch(() => toast.error('Erreur lors du chargement des entretiens'))
      .finally(() => setLoading(false))
  }, [])

  const isEchu = (entretien) => {
    const now = new Date()
    if (entretien.dateProchaine && new Date(entretien.dateProchaine) < now) return true
    return false
  }

  const columns = [
    { key: 'type', label: 'Type', sortable: true },
    {
      key: 'vehicule',
      label: 'Véhicule',
      render: (v) => v?.immatriculation ?? '—',
      sortable: false,
    },
    {
      key: 'dateRealise',
      label: 'Réalisé le',
      sortable: true,
      render: (v) => v ? new Date(v).toLocaleDateString('fr-FR') : '—',
    },
    {
      key: 'dateProchaine',
      label: 'Prochain',
      sortable: true,
      render: (v, row) => {
        if (!v) return '—'
        const date = new Date(v).toLocaleDateString('fr-FR')
        return isEchu(row)
          ? <span className="text-danger font-medium">{date} ⚠️</span>
          : date
      },
    },
    {
      key: 'cout',
      label: 'Coût',
      render: (v) => v != null ? `${parseFloat(v).toLocaleString('fr-FR')} €` : '—',
    },
    {
      key: 'statut_echu',
      label: 'État',
      render: (_, row) => isEchu(row)
        ? <Badge value="alerte">Échu</Badge>
        : <Badge value="disponible">OK</Badge>,
    },
    {
      key: 'actions',
      label: '',
      render: (_, row) => isGestionnaire() ? (
        <Button size="sm" variant="secondary" onClick={() => navigate(`/entretiens/${row.id}/modifier`)}>
          Modifier
        </Button>
      ) : null,
    },
  ]

  return (
    <div className="space-y-4">
      <div className="flex items-center justify-between">
        <div>
          <h2 className="text-xl font-semibold text-dark">Entretiens</h2>
          <p className="text-sm text-gray-500">{entretiens.length} entretien(s)</p>
        </div>
        {isGestionnaire() && (
          <Link to="/entretiens/nouveau">
            <Button>+ Planifier un entretien</Button>
          </Link>
        )}
      </div>
      <Table columns={columns} data={entretiens} loading={loading} emptyMessage="Aucun entretien enregistré" />
    </div>
  )
}
