import { useLocation } from 'react-router-dom'
import { useAuthStore } from '../../store/authStore'

const titles = {
  '/dashboard': 'Dashboard',
  '/vehicules': 'Véhicules',
  '/entretiens': 'Entretiens',
  '/alertes': 'Alertes',
  '/admin': 'Administration',
  '/admin/utilisateurs': 'Utilisateurs',
  '/admin/categories': 'Catégories',
}

export default function TopBar() {
  const location = useLocation()
  const user = useAuthStore((s) => s.user)

  const title = Object.entries(titles)
    .filter(([path]) => location.pathname.startsWith(path))
    .sort((a, b) => b[0].length - a[0].length)[0]?.[1] ?? 'OptiFleet'

  return (
    <header className="h-16 bg-white border-b border-gray-100 flex items-center justify-between px-6 content-offset sticky top-0 z-20">
      <h1 className="text-lg font-semibold text-dark">{title}</h1>
      <div className="flex items-center gap-4">
        <span className="text-sm text-gray-500">
          {user?.prenom} {user?.nom}
        </span>
        <span
          className={[
            'px-2 py-0.5 rounded-full text-xs font-medium',
            user?.role === 'ROLE_ADMIN' ? 'bg-primary/10 text-primary' :
            user?.role === 'ROLE_GESTIONNAIRE' ? 'bg-teal/10 text-teal-fleet' :
            'bg-gray-100 text-gray-600',
          ].join(' ')}
        >
          {user?.role?.replace('ROLE_', '') ?? ''}
        </span>
      </div>
    </header>
  )
}
