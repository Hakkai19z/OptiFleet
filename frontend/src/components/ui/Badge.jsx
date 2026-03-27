const variants = {
  disponible:  'bg-green-100 text-green-800 border border-green-200',
  en_mission:  'bg-blue-100 text-blue-800 border border-blue-200',
  maintenance: 'bg-amber-100 text-amber-800 border border-amber-200',
  inactif:     'bg-gray-100 text-gray-600 border border-gray-200',
  alerte:      'bg-red-100 text-red-700 border border-red-200',
  en_attente:  'bg-amber-100 text-amber-800 border border-amber-200',
  en_cours:    'bg-blue-100 text-blue-800 border border-blue-200',
  resolue:     'bg-green-100 text-green-800 border border-green-200',
  default:     'bg-gray-100 text-gray-700 border border-gray-200',
}

const labels = {
  disponible:  'Disponible',
  en_mission:  'En mission',
  maintenance: 'Maintenance',
  inactif:     'Inactif',
  alerte:      'Alerte',
  en_attente:  'En attente',
  en_cours:    'En cours',
  resolue:     'Résolue',
}

export default function Badge({ value, children, className = '' }) {
  const key = value || 'default'
  const style = variants[key] || variants.default
  const label = children || labels[key] || value

  return (
    <span
      className={[
        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
        style,
        className,
      ].join(' ')}
    >
      {label}
    </span>
  )
}
