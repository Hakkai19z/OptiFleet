import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom'
import { useAuthStore } from './store/authStore'
import Layout from './components/layout/Layout'
import Login from './pages/Login'
import Dashboard from './pages/Dashboard'
import ListeVehicules from './pages/Vehicules/ListeVehicules'
import DetailVehicule from './pages/Vehicules/DetailVehicule'
import FormulaireVehicule from './pages/Vehicules/FormulaireVehicule'
import ListeEntretiens from './pages/Entretiens/ListeEntretiens'
import ListeAlertes from './pages/Alertes/ListeAlertes'
import AdminUtilisateurs from './pages/Admin/AdminUtilisateurs'
import AdminCategories from './pages/Admin/AdminCategories'

function RequireAuth({ children }) {
  const token = useAuthStore((s) => s.token)
  if (!token) return <Navigate to="/login" replace />
  return children
}

function RequireAdmin({ children }) {
  const user = useAuthStore((s) => s.user)
  if (!user || user.role !== 'ROLE_ADMIN') return <Navigate to="/dashboard" replace />
  return children
}

export default function App() {
  return (
    <BrowserRouter>
      <Routes>
        <Route path="/login" element={<Login />} />

        <Route
          path="/"
          element={
            <RequireAuth>
              <Layout />
            </RequireAuth>
          }
        >
          <Route index element={<Navigate to="/dashboard" replace />} />
          <Route path="dashboard" element={<Dashboard />} />

          <Route path="vehicules" element={<ListeVehicules />} />
          <Route path="vehicules/nouveau" element={<FormulaireVehicule />} />
          <Route path="vehicules/:id" element={<DetailVehicule />} />
          <Route path="vehicules/:id/modifier" element={<FormulaireVehicule />} />

          <Route path="entretiens" element={<ListeEntretiens />} />

          <Route path="alertes" element={<ListeAlertes />} />

          <Route
            path="admin"
            element={
              <RequireAdmin>
                <Navigate to="/admin/utilisateurs" replace />
              </RequireAdmin>
            }
          />
          <Route
            path="admin/utilisateurs"
            element={
              <RequireAdmin>
                <AdminUtilisateurs />
              </RequireAdmin>
            }
          />
          <Route
            path="admin/categories"
            element={
              <RequireAdmin>
                <AdminCategories />
              </RequireAdmin>
            }
          />

          <Route path="*" element={<Navigate to="/dashboard" replace />} />
        </Route>
      </Routes>
    </BrowserRouter>
  )
}
