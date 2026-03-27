import { useState, useEffect } from 'react'
import Table from '../../components/ui/Table'
import Button from '../../components/ui/Button'
import Input from '../../components/ui/Input'
import Card, { CardBody, CardHeader } from '../../components/ui/Card'
import api from '../../services/api'
import { useToastStore } from '../../store/toastStore'

export default function AdminCategories() {
  const [categories, setCategories] = useState([])
  const [loading, setLoading] = useState(true)
  const [showForm, setShowForm] = useState(false)
  const [form, setForm] = useState({ libelle: '', description: '' })
  const [saving, setSaving] = useState(false)
  const toast = useToastStore()

  const fetchCategories = () => {
    setLoading(true)
    api.get('/categories')
      .then(({ data }) => setCategories(data['hydra:member'] ?? data))
      .catch(() => toast.error('Impossible de charger les catégories'))
      .finally(() => setLoading(false))
  }

  useEffect(() => { fetchCategories() }, [])

  const handleSubmit = async (e) => {
    e.preventDefault()
    if (!form.libelle.trim()) return
    setSaving(true)
    try {
      await api.post('/categories', form)
      toast.success('Catégorie créée')
      setForm({ libelle: '', description: '' })
      setShowForm(false)
      fetchCategories()
    } catch (err) {
      toast.error(err.response?.data?.['hydra:description'] ?? 'Erreur')
    } finally {
      setSaving(false)
    }
  }

  const handleDelete = async (id, libelle) => {
    if (!window.confirm(`Supprimer la catégorie "${libelle}" ?`)) return
    try {
      await api.delete(`/categories/${id}`)
      toast.success('Catégorie supprimée')
      setCategories((c) => c.filter((x) => x.id !== id))
    } catch {
      toast.error('Impossible de supprimer (véhicules rattachés ?)')
    }
  }

  const columns = [
    { key: 'libelle', label: 'Libellé', sortable: true },
    { key: 'description', label: 'Description', render: (v) => v ?? '—' },
    {
      key: 'actions',
      label: '',
      render: (_, row) => (
        <Button size="sm" variant="danger" onClick={() => handleDelete(row.id, row.libelle)}>
          Supprimer
        </Button>
      ),
    },
  ]

  return (
    <div className="space-y-4">
      <div className="flex items-center justify-between">
        <div>
          <h2 className="text-xl font-semibold text-dark">Catégories</h2>
          <p className="text-sm text-gray-500">{categories.length} catégorie(s)</p>
        </div>
        <Button onClick={() => setShowForm(!showForm)}>
          {showForm ? 'Annuler' : '+ Nouvelle catégorie'}
        </Button>
      </div>

      {showForm && (
        <Card>
          <CardHeader><h3 className="font-medium text-dark">Nouvelle catégorie</h3></CardHeader>
          <CardBody>
            <form onSubmit={handleSubmit} className="flex gap-3 items-end">
              <Input
                label="Libellé"
                value={form.libelle}
                onChange={(e) => setForm((f) => ({ ...f, libelle: e.target.value }))}
                required
                className="flex-1"
              />
              <Input
                label="Description"
                value={form.description}
                onChange={(e) => setForm((f) => ({ ...f, description: e.target.value }))}
                className="flex-1"
              />
              <Button type="submit" loading={saving}>Créer</Button>
            </form>
          </CardBody>
        </Card>
      )}

      <Table columns={columns} data={categories} loading={loading} emptyMessage="Aucune catégorie" />
    </div>
  )
}
