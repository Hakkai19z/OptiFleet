import { Link } from 'react-router-dom'
import Card, { CardBody } from './Card'
import Badge from './Badge'

export default function VehiculeCard({ vehicule }) {
  const { id, immatriculation, marque, modele, annee, kilometrage, statut, categorie } = vehicule

  const accentMap = {
    disponible:  'teal',
    en_mission:  'blue',
    maintenance: 'amber',
    inactif:     'primary',
  }

  return (
    <Card accent={accentMap[statut] ?? 'primary'} className="hover:shadow-md transition-shadow">
      <CardBody>
        <div className="flex items-start justify-between mb-3">
          <div>
            <h3 className="font-semibold text-dark">
              {marque} {modele}
            </h3>
            <p className="text-xs text-gray-500 mt-0.5">{immatriculation}</p>
          </div>
          <Badge value={statut} />
        </div>

        <dl className="space-y-1.5 text-sm">
          <div className="flex justify-between">
            <dt className="text-gray-500">Année</dt>
            <dd className="font-medium text-dark">{annee}</dd>
          </div>
          <div className="flex justify-between">
            <dt className="text-gray-500">Kilométrage</dt>
            <dd className="font-medium text-dark">{kilometrage?.toLocaleString('fr-FR')} km</dd>
          </div>
          {categorie && (
            <div className="flex justify-between">
              <dt className="text-gray-500">Catégorie</dt>
              <dd className="font-medium text-dark">{categorie.libelle}</dd>
            </div>
          )}
        </dl>

        <Link
          to={`/vehicules/${id}`}
          className="mt-4 block text-center text-xs font-medium text-primary hover:underline"
        >
          Voir le détail →
        </Link>
      </CardBody>
    </Card>
  )
}
