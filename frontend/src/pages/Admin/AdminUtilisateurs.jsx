import { useState, useEffect } from 'react'
import Table from '../../components/ui/Table'
import Badge from '../../components/ui/Badge'
import api from '../../services/api'
import { useToastStore } from '../../store/toastStore'

const roleBadge = { ROLE_ADMIN: 'alerte', ROLE_GESTIONNAIRE: 'en_mission', ROLE_CONDUCTEUR: 'disponible' }

export default function AdminUtilisateurs() {
  const [utilisateurs, setUtilisateurs] = useState([])
  const [loading, setLoading] = useState(true)
  const toast = useToastStore()

  useEffect(() => {
    api.get('/utilisateurs')
      .then(({ data }) => setUtilisateurs(data['hydra:member'] ?? data))
      .catch(() => toast.error('Impossible de charger les utilisateurs'))
      .finally(() => setLoading(false))
  }, [])

  const columns = [
    { key: 'nom', label: 'Nom', sortable: true },
    { key: 'prenom', label: 'Prénom', sortable: true },
    { key: 'email', label: 'Email', sortable: true },
    {
      key: 'role',
      label: 'Rôle',
      render: (v) => <Badge value={roleBadge[v] ?? 'default'}>{v?.replace('ROLE_', '') ?? v}</Badge>,
    },
    {
      key: 'createdAt',
      label: 'Créé le',
      render: (v) => v ? new Date(v).toLocaleDateString('fr-FR') : '—',
    },
  ]

  return (
    <div className="space-y-4">
      <div>
        <h2 className="text-xl font-semibold text-dark">Utilisateurs</h2>
        <p className="text-sm text-gray-500">{utilisateurs.length} utilisateur(s)</p>
      </div>
      <Table columns={columns} data={utilisateurs} loading={loading} emptyMessage="Aucun utilisateur" />
    </div>
  )
}
