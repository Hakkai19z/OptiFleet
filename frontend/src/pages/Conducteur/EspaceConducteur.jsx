import { useEffect, useState } from 'react'
import Card, { CardBody, CardHeader } from '../../components/ui/Card'
import Badge from '../../components/ui/Badge'
import { useAuthStore } from '../../store/authStore'
import api from '../../services/api'
import { useToastStore } from '../../store/toastStore'
import { formatDate, formatKm } from '../../utils/formatters'

export default function EspaceConducteur() {
  const user = useAuthStore((s) => s.user)
  const [affectations, setAffectations] = useState([])
  const [loading, setLoading] = useState(true)
  const toast = useToastStore()

  useEffect(() => {
    api.get('/affectations', { params: { conducteur: user?.id } })
      .then(({ data }) => setAffectations(data['hydra:member'] ?? data))
      .catch(() => toast.error('Impossible de charger vos affectations'))
      .finally(() => setLoading(false))
  }, [user?.id])

  const affectationsActives = affectations.filter((a) => !a.dateFin)
  const affectationsTerminees = affectations.filter((a) => !!a.dateFin)

  return (
    <div className="space-y-6">
      <div>
        <h2 className="text-xl font-semibold text-dark">
          Bonjour, {user?.prenom} {user?.nom}
        </h2>
        <p className="text-sm text-gray-500">Espace conducteur — vos affectations</p>
      </div>

      <Card accent="teal">
        <CardHeader>
          <h3 className="font-semibold text-dark">
            Affectation(s) en cours ({affectationsActives.length})
          </h3>
        </CardHeader>
        <CardBody>
          {loading ? (
            <div className="animate-pulse h-16 bg-gray-100 rounded" />
          ) : affectationsActives.length === 0 ? (
            <p className="text-sm text-gray-500">Aucune affectation active.</p>
          ) : (
            <div className="space-y-4">
              {affectationsActives.map((a) => (
                <div key={a.id} className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                  <div>
                    <p className="font-medium text-dark">
                      {a.vehicule?.marque} {a.vehicule?.modele}
                    </p>
                    <p className="text-xs text-gray-500">
                      {a.vehicule?.immatriculation} • Depuis le {formatDate(a.dateDebut)}
                    </p>
                    {a.vehicule?.kilometrage && (
                      <p className="text-xs text-gray-400">{formatKm(a.vehicule.kilometrage)}</p>
                    )}
                  </div>
                  <Badge value="en_mission" />
                </div>
              ))}
            </div>
          )}
        </CardBody>
      </Card>

      {affectationsTerminees.length > 0 && (
        <Card>
          <CardHeader>
            <h3 className="font-semibold text-dark">
              Historique ({affectationsTerminees.length})
            </h3>
          </CardHeader>
          <CardBody>
            <div className="space-y-3">
              {affectationsTerminees.slice(0, 5).map((a) => (
                <div key={a.id} className="flex items-center justify-between text-sm">
                  <span className="text-dark">
                    {a.vehicule?.marque} {a.vehicule?.modele} ({a.vehicule?.immatriculation})
                  </span>
                  <span className="text-gray-400 text-xs">
                    {formatDate(a.dateDebut)} — {formatDate(a.dateFin)}
                  </span>
                </div>
              ))}
            </div>
          </CardBody>
        </Card>
      )}
    </div>
  )
}
