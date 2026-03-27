import { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, PieChart, Pie, Cell } from 'recharts'
import Card, { CardBody, CardHeader } from '../components/ui/Card'
import { SkeletonCard } from '../components/ui/Skeleton'
import { dashboardService } from '../services/dashboardService'
import { useToastStore } from '../store/toastStore'

const COLORS = ['#22C55E', '#3B82F6', '#F59E0B', '#9CA3AF']

export default function Dashboard() {
  const [stats, setStats] = useState(null)
  const [loading, setLoading] = useState(true)
  const toast = useToastStore()

  useEffect(() => {
    dashboardService.getStats()
      .then(setStats)
      .catch(() => toast.error('Impossible de charger les statistiques'))
      .finally(() => setLoading(false))
  }, [])

  if (loading) {
    return (
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {Array.from({ length: 4 }).map((_, i) => <SkeletonCard key={i} />)}
      </div>
    )
  }

  const { vehicules, alertes, maintenance, taux_disponibilite } = stats ?? {}

  const kpis = [
    {
      label: 'Véhicules disponibles',
      value: vehicules?.disponible ?? 0,
      total: vehicules?.total ?? 0,
      accent: 'teal',
      icon: '🚗',
      to: '/vehicules?statut=disponible',
    },
    {
      label: 'En mission',
      value: vehicules?.en_mission ?? 0,
      total: vehicules?.total ?? 0,
      accent: 'blue',
      icon: '🛣️',
      to: '/vehicules?statut=en_mission',
    },
    {
      label: 'Alertes actives',
      value: alertes?.actives ?? 0,
      accent: 'danger',
      icon: '⚠️',
      to: '/alertes',
    },
    {
      label: 'Taux disponibilité',
      value: `${taux_disponibilite ?? 0}%`,
      accent: 'primary',
      icon: '📊',
    },
  ]

  const pieData = [
    { name: 'Disponible', value: vehicules?.disponible ?? 0 },
    { name: 'En mission', value: vehicules?.en_mission ?? 0 },
    { name: 'Maintenance', value: vehicules?.maintenance ?? 0 },
    { name: 'Inactif', value: vehicules?.inactif ?? 0 },
  ]

  const barData = [
    { name: 'Disp.', value: vehicules?.disponible ?? 0 },
    { name: 'Mission', value: vehicules?.en_mission ?? 0 },
    { name: 'Maint.', value: vehicules?.maintenance ?? 0 },
    { name: 'Inactif', value: vehicules?.inactif ?? 0 },
  ]

  return (
    <div className="space-y-6">
      {/* KPI Cards */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {kpis.map((kpi) => (
          <Card key={kpi.label} accent={kpi.accent}>
            <CardBody>
              <div className="flex items-start justify-between">
                <div>
                  <p className="text-xs font-medium text-gray-500 uppercase tracking-wide">{kpi.label}</p>
                  <p className="text-3xl font-bold text-dark mt-1">{kpi.value}</p>
                  {kpi.total != null && (
                    <p className="text-xs text-gray-400 mt-0.5">sur {kpi.total} véhicules</p>
                  )}
                </div>
                <span className="text-2xl">{kpi.icon}</span>
              </div>
              {kpi.to && (
                <Link to={kpi.to} className="text-xs text-primary hover:underline mt-3 inline-block">
                  Voir →
                </Link>
              )}
            </CardBody>
          </Card>
        ))}
      </div>

      {/* Charts */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <Card>
          <CardHeader>
            <h2 className="font-semibold text-dark">Répartition du parc</h2>
          </CardHeader>
          <CardBody>
            <ResponsiveContainer width="100%" height={220}>
              <BarChart data={barData}>
                <CartesianGrid strokeDasharray="3 3" stroke="#F3F4F6" />
                <XAxis dataKey="name" tick={{ fontSize: 12 }} />
                <YAxis tick={{ fontSize: 12 }} />
                <Tooltip />
                <Bar dataKey="value" fill="#534AB7" radius={[4, 4, 0, 0]} />
              </BarChart>
            </ResponsiveContainer>
          </CardBody>
        </Card>

        <Card>
          <CardHeader>
            <h2 className="font-semibold text-dark">Statuts (camembert)</h2>
          </CardHeader>
          <CardBody className="flex items-center justify-center">
            <ResponsiveContainer width="100%" height={220}>
              <PieChart>
                <Pie
                  data={pieData.filter(d => d.value > 0)}
                  cx="50%"
                  cy="50%"
                  innerRadius={55}
                  outerRadius={85}
                  paddingAngle={4}
                  dataKey="value"
                  label={({ name, percent }) => `${name} ${(percent * 100).toFixed(0)}%`}
                  labelLine={false}
                >
                  {pieData.map((entry, index) => (
                    <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
                  ))}
                </Pie>
                <Tooltip />
              </PieChart>
            </ResponsiveContainer>
          </CardBody>
        </Card>
      </div>

      {/* Coût maintenance */}
      <Card accent="amber">
        <CardBody>
          <div className="flex items-center justify-between">
            <div>
              <p className="text-xs font-medium text-gray-500 uppercase tracking-wide">Coût maintenance (12 mois)</p>
              <p className="text-3xl font-bold text-dark mt-1">
                {(maintenance?.cout_12_mois ?? 0).toLocaleString('fr-FR', { style: 'currency', currency: 'EUR' })}
              </p>
            </div>
            <span className="text-3xl">🔧</span>
          </div>
        </CardBody>
      </Card>
    </div>
  )
}
