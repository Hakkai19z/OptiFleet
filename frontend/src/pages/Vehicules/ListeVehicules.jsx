import { useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import Table from '../../components/ui/Table'
import Badge from '../../components/ui/Badge'
import Button from '../../components/ui/Button'
import { useVehicules } from '../../hooks/useVehicules'
import { useAuthStore } from '../../store/authStore'

export default function ListeVehicules() {
  const [filtreStatut, setFiltreStatut] = useState('')
  const { vehicules, loading, remove } = useVehicules(filtreStatut ? { statut: filtreStatut } : {})
  const navigate = useNavigate()
  const { isGestionnaire, isAdmin } = useAuthStore.getState()

  const columns = [
    { key: 'immatriculation', label: 'Immatriculation', sortable: true },
    { key: 'marque', label: 'Marque', sortable: true },
    { key: 'modele', label: 'Modèle', sortable: true },
    { key: 'annee', label: 'Année', sortable: true },
    {
      key: 'kilometrage',
      label: 'Kilométrage',
      sortable: true,
      render: (v) => v?.toLocaleString('fr-FR') + ' km',
    },
    {
      key: 'statut',
      label: 'Statut',
      render: (v) => <Badge value={v} />,
    },
    {
      key: 'categorie',
      label: 'Catégorie',
      render: (v) => v?.libelle ?? '—',
    },
    {
      key: 'actions',
      label: '',
      render: (_, row) => (
        <div className="flex gap-2">
          <Button size="sm" variant="ghost" onClick={() => navigate(`/vehicules/${row.id}`)}>
            Voir
          </Button>
          {isGestionnaire() && (
            <Button size="sm" variant="secondary" onClick={() => navigate(`/vehicules/${row.id}/modifier`)}>
              Modifier
            </Button>
          )}
          {isAdmin() && (
            <Button
              size="sm"
              variant="danger"
              onClick={() => {
                if (window.confirm(`Supprimer le véhicule ${row.immatriculation} ?`)) {
                  remove(row.id)
                }
              }}
            >
              Supprimer
            </Button>
          )}
        </div>
      ),
    },
  ]

  return (
    <div className="space-y-4">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h2 className="text-xl font-semibold text-dark">Parc véhicules</h2>
          <p className="text-sm text-gray-500">{vehicules.length} véhicule(s)</p>
        </div>
        {isGestionnaire() && (
          <Link to="/vehicules/nouveau">
            <Button>+ Ajouter un véhicule</Button>
          </Link>
        )}
      </div>

      {/* Filtres */}
      <div className="flex gap-2 flex-wrap">
        {['', 'disponible', 'en_mission', 'maintenance', 'inactif'].map((s) => (
          <button
            key={s}
            onClick={() => setFiltreStatut(s)}
            className={[
              'px-3 py-1.5 rounded-full text-xs font-medium border transition-all',
              filtreStatut === s
                ? 'bg-primary text-white border-primary'
                : 'border-gray-200 text-gray-600 hover:border-primary hover:text-primary',
            ].join(' ')}
          >
            {s === '' ? 'Tous' : s.replace('_', ' ')}
          </button>
        ))}
      </div>

      <Table
        columns={columns}
        data={vehicules}
        loading={loading}
        emptyMessage="Aucun véhicule trouvé"
      />
    </div>
  )
}
