import { useState } from 'react'
import Table from '../../components/ui/Table'
import Badge from '../../components/ui/Badge'
import Button from '../../components/ui/Button'
import { useAlertes } from '../../hooks/useAlertes'
import { useAuthStore } from '../../store/authStore'

export default function ListeAlertes() {
  const [filtreStatut, setFiltreStatut] = useState('en_attente')
  const { alertes, loading, resoudre } = useAlertes(filtreStatut ? { statut: filtreStatut } : {})
  const { isGestionnaire } = useAuthStore.getState()

  const columns = [
    { key: 'type', label: 'Type', sortable: true },
    {
      key: 'vehicule',
      label: 'Véhicule',
      render: (v) => `${v?.marque ?? ''} ${v?.modele ?? ''} (${v?.immatriculation ?? '—'})`,
    },
    { key: 'message', label: 'Message' },
    {
      key: 'dateEcheance',
      label: 'Échéance',
      sortable: true,
      render: (v) => v ? new Date(v).toLocaleDateString('fr-FR') : '—',
    },
    {
      key: 'statut',
      label: 'Statut',
      render: (v) => <Badge value={v} />,
    },
    {
      key: 'actions',
      label: '',
      render: (_, row) =>
        isGestionnaire() && row.statut !== 'resolue' ? (
          <Button
            size="sm"
            variant="secondary"
            onClick={() => resoudre(row.id)}
          >
            Résoudre
          </Button>
        ) : null,
    },
  ]

  const filtres = [
    { value: '', label: 'Toutes' },
    { value: 'en_attente', label: 'En attente' },
    { value: 'en_cours', label: 'En cours' },
    { value: 'resolue', label: 'Résolues' },
  ]

  return (
    <div className="space-y-4">
      <div className="flex items-center justify-between">
        <div>
          <h2 className="text-xl font-semibold text-dark">Alertes</h2>
          <p className="text-sm text-gray-500">{alertes.length} alerte(s)</p>
        </div>
      </div>

      <div className="flex gap-2 flex-wrap">
        {filtres.map((f) => (
          <button
            key={f.value}
            onClick={() => setFiltreStatut(f.value)}
            className={[
              'px-3 py-1.5 rounded-full text-xs font-medium border transition-all',
              filtreStatut === f.value
                ? 'bg-primary text-white border-primary'
                : 'border-gray-200 text-gray-600 hover:border-primary hover:text-primary',
            ].join(' ')}
          >
            {f.label}
          </button>
        ))}
      </div>

      <Table
        columns={columns}
        data={alertes}
        loading={loading}
        emptyMessage="Aucune alerte"
      />
    </div>
  )
}
