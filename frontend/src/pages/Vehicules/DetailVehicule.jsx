import { useEffect, useState } from 'react'
import { useParams, Link } from 'react-router-dom'
import Card, { CardBody, CardHeader } from '../../components/ui/Card'
import Badge from '../../components/ui/Badge'
import Button from '../../components/ui/Button'
import { vehiculeService } from '../../services/vehiculeService'
import { useToastStore } from '../../store/toastStore'

export default function DetailVehicule() {
  const { id } = useParams()
  const [vehicule, setVehicule] = useState(null)
  const [loading, setLoading] = useState(true)
  const toast = useToastStore()

  useEffect(() => {
    vehiculeService.getById(id)
      .then(setVehicule)
      .catch(() => toast.error('Véhicule introuvable'))
      .finally(() => setLoading(false))
  }, [id])

  if (loading) return <div className="animate-pulse h-64 bg-gray-100 rounded-xl" />
  if (!vehicule) return <p className="text-gray-500">Véhicule introuvable.</p>

  return (
    <div className="space-y-4 max-w-2xl">
      <div className="flex items-center justify-between">
        <Link to="/vehicules" className="text-sm text-gray-500 hover:text-primary">← Retour</Link>
        <Link to={`/vehicules/${id}/modifier`}>
          <Button size="sm" variant="secondary">Modifier</Button>
        </Link>
      </div>

      <Card accent="primary">
        <CardHeader>
          <div className="flex items-center justify-between">
            <h2 className="font-semibold text-dark text-lg">
              {vehicule.marque} {vehicule.modele}
            </h2>
            <Badge value={vehicule.statut} />
          </div>
          <p className="text-sm text-gray-500 mt-1">{vehicule.immatriculation}</p>
        </CardHeader>
        <CardBody>
          <dl className="grid grid-cols-2 gap-x-6 gap-y-4">
            {[
              ['Immatriculation', vehicule.immatriculation],
              ['Marque', vehicule.marque],
              ['Modèle', vehicule.modele],
              ['Année', vehicule.annee],
              ['Kilométrage', vehicule.kilometrage?.toLocaleString('fr-FR') + ' km'],
              ['Catégorie', vehicule.categorie?.libelle ?? '—'],
              ['Adresse', vehicule.adresse ?? '—'],
              ['Latitude', vehicule.latitude ?? '—'],
              ['Longitude', vehicule.longitude ?? '—'],
            ].map(([label, value]) => (
              <div key={label}>
                <dt className="text-xs font-medium text-gray-500">{label}</dt>
                <dd className="mt-0.5 text-sm text-dark">{value}</dd>
              </div>
            ))}
          </dl>
        </CardBody>
      </Card>
    </div>
  )
}
