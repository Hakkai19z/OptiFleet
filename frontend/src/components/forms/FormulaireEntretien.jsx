import { useState, useEffect } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import Input from '../ui/Input'
import Button from '../ui/Button'
import Card, { CardBody, CardHeader } from '../ui/Card'
import { entretienService } from '../../services/entretienService'
import { vehiculeService } from '../../services/vehiculeService'
import { useToastStore } from '../../store/toastStore'

const TYPES = ['revision', 'vidange', 'CT', 'freins', 'pneus', 'autre']

export default function FormulaireEntretien() {
  const { id } = useParams()
  const isEdit = !!id
  const navigate = useNavigate()
  const toast = useToastStore()

  const [vehicules, setVehicules] = useState([])
  const [form, setForm] = useState({
    type: 'revision',
    dateRealise: new Date().toISOString().split('T')[0],
    dateProchaine: '',
    kmProchaine: '',
    cout: '',
    notes: '',
    vehicule: '',
  })
  const [errors, setErrors] = useState({})
  const [loading, setLoading] = useState(false)

  useEffect(() => {
    vehiculeService.getAll().then((data) => setVehicules(data['hydra:member'] ?? data))

    if (isEdit) {
      entretienService.getById(id).then((e) => {
        setForm({
          type: e.type,
          dateRealise: e.dateRealise?.split('T')[0] ?? '',
          dateProchaine: e.dateProchaine?.split('T')[0] ?? '',
          kmProchaine: e.kmProchaine ?? '',
          cout: e.cout ?? '',
          notes: e.notes ?? '',
          vehicule: `/api/vehicules/${e.vehicule?.id}`,
        })
      })
    }
  }, [id])

  const handleChange = (field) => (e) => {
    setForm((f) => ({ ...f, [field]: e.target.value }))
    setErrors((err) => ({ ...err, [field]: undefined }))
  }

  const validate = () => {
    const e = {}
    if (!form.vehicule) e.vehicule = 'Requis'
    if (!form.dateRealise) e.dateRealise = 'Requis'
    setErrors(e)
    return Object.keys(e).length === 0
  }

  const handleSubmit = async (e) => {
    e.preventDefault()
    if (!validate()) return
    setLoading(true)
    const payload = {
      ...form,
      kmProchaine: form.kmProchaine ? parseInt(form.kmProchaine) : null,
      cout: form.cout ? form.cout : null,
      dateProchaine: form.dateProchaine || null,
    }
    try {
      if (isEdit) {
        await entretienService.update(id, payload)
        toast.success('Entretien modifié')
      } else {
        await entretienService.create(payload)
        toast.success('Entretien planifié')
      }
      navigate('/entretiens')
    } catch (err) {
      toast.error(err.response?.data?.['hydra:description'] ?? 'Erreur')
    } finally {
      setLoading(false)
    }
  }

  return (
    <Card className="max-w-2xl mx-auto">
      <CardHeader>
        <h2 className="font-semibold text-dark">{isEdit ? 'Modifier l\'entretien' : 'Planifier un entretien'}</h2>
      </CardHeader>
      <CardBody>
        <form onSubmit={handleSubmit} className="space-y-4">
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label className="form-label">Véhicule <span className="text-danger">*</span></label>
              <select
                value={form.vehicule}
                onChange={handleChange('vehicule')}
                className="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:border-primary focus:ring-primary/20"
              >
                <option value="">Sélectionner...</option>
                {vehicules.map((v) => (
                  <option key={v.id} value={`/api/vehicules/${v.id}`}>
                    {v.immatriculation} — {v.marque} {v.modele}
                  </option>
                ))}
              </select>
              {errors.vehicule && <p className="form-error">{errors.vehicule}</p>}
            </div>

            <div>
              <label className="form-label">Type <span className="text-danger">*</span></label>
              <select
                value={form.type}
                onChange={handleChange('type')}
                className="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:border-primary focus:ring-primary/20"
              >
                {TYPES.map((t) => <option key={t} value={t}>{t}</option>)}
              </select>
            </div>

            <Input label="Date réalisée" type="date" value={form.dateRealise} onChange={handleChange('dateRealise')} error={errors.dateRealise} required />
            <Input label="Prochain entretien (date)" type="date" value={form.dateProchaine} onChange={handleChange('dateProchaine')} />
            <Input label="Prochain entretien (km)" type="number" value={form.kmProchaine} onChange={handleChange('kmProchaine')} min={0} />
            <Input label="Coût (€)" type="number" value={form.cout} onChange={handleChange('cout')} min={0} step="0.01" />
          </div>
          <Input label="Notes" value={form.notes} onChange={handleChange('notes')} />

          <div className="flex gap-3 pt-2">
            <Button type="submit" loading={loading}>{isEdit ? 'Enregistrer' : 'Planifier'}</Button>
            <Button type="button" variant="ghost" onClick={() => navigate('/entretiens')}>Annuler</Button>
          </div>
        </form>
      </CardBody>
    </Card>
  )
}
