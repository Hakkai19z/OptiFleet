import { useState, useEffect } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import Input from '../../components/ui/Input'
import Button from '../../components/ui/Button'
import Card, { CardBody, CardHeader } from '../../components/ui/Card'
import { vehiculeService } from '../../services/vehiculeService'
import { useToastStore } from '../../store/toastStore'

const STATUTS = ['disponible', 'en_mission', 'maintenance', 'inactif']

export default function FormulaireVehicule() {
  const { id } = useParams()
  const isEdit = !!id
  const navigate = useNavigate()
  const toast = useToastStore()

  const [form, setForm] = useState({
    immatriculation: '',
    marque: '',
    modele: '',
    annee: new Date().getFullYear(),
    kilometrage: 0,
    statut: 'disponible',
    adresse: '',
  })
  const [errors, setErrors] = useState({})
  const [loading, setLoading] = useState(false)
  const [fetching, setFetching] = useState(isEdit)

  useEffect(() => {
    if (!isEdit) return
    vehiculeService.getById(id)
      .then((v) => setForm({
        immatriculation: v.immatriculation,
        marque: v.marque,
        modele: v.modele,
        annee: v.annee,
        kilometrage: v.kilometrage,
        statut: v.statut,
        adresse: v.adresse ?? '',
      }))
      .catch(() => toast.error('Impossible de charger le véhicule'))
      .finally(() => setFetching(false))
  }, [id])

  const validate = () => {
    const e = {}
    if (!form.immatriculation) e.immatriculation = 'Requis'
    else if (!/^[A-Z]{2}-[0-9]{3}-[A-Z]{2}$/.test(form.immatriculation))
      e.immatriculation = 'Format invalide (ex: AB-123-CD)'
    if (!form.marque) e.marque = 'Requis'
    if (!form.modele) e.modele = 'Requis'
    if (!form.annee || form.annee < 1900 || form.annee > 2100) e.annee = 'Année invalide'
    if (form.kilometrage < 0) e.kilometrage = 'Doit être >= 0'
    setErrors(e)
    return Object.keys(e).length === 0
  }

  const handleChange = (field) => (e) => {
    setForm((f) => ({ ...f, [field]: e.target.value }))
    setErrors((err) => ({ ...err, [field]: undefined }))
  }

  const handleSubmit = async (e) => {
    e.preventDefault()
    if (!validate()) return
    setLoading(true)
    try {
      if (isEdit) {
        await vehiculeService.update(id, form)
        toast.success('Véhicule modifié avec succès')
      } else {
        await vehiculeService.create(form)
        toast.success('Véhicule créé avec succès')
      }
      navigate('/vehicules')
    } catch (err) {
      const msg = err.response?.data?.['hydra:description'] ?? 'Erreur lors de la sauvegarde'
      toast.error(msg)
    } finally {
      setLoading(false)
    }
  }

  if (fetching) return <div className="animate-pulse h-64 bg-gray-100 rounded-xl" />

  return (
    <Card className="max-w-2xl mx-auto">
      <CardHeader>
        <h2 className="font-semibold text-dark">
          {isEdit ? 'Modifier le véhicule' : 'Nouveau véhicule'}
        </h2>
      </CardHeader>
      <CardBody>
        <form onSubmit={handleSubmit} className="space-y-4">
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <Input
              label="Immatriculation"
              value={form.immatriculation}
              onChange={handleChange('immatriculation')}
              error={errors.immatriculation}
              placeholder="AB-123-CD"
              required
              disabled={isEdit}
            />
            <div>
              <label className="form-label">Statut <span className="text-danger">*</span></label>
              <select
                value={form.statut}
                onChange={handleChange('statut')}
                className="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:border-primary focus:ring-primary/20"
              >
                {STATUTS.map((s) => (
                  <option key={s} value={s}>{s.replace('_', ' ')}</option>
                ))}
              </select>
            </div>
            <Input
              label="Marque"
              value={form.marque}
              onChange={handleChange('marque')}
              error={errors.marque}
              required
            />
            <Input
              label="Modèle"
              value={form.modele}
              onChange={handleChange('modele')}
              error={errors.modele}
              required
            />
            <Input
              label="Année"
              type="number"
              value={form.annee}
              onChange={handleChange('annee')}
              error={errors.annee}
              min={1900}
              max={2100}
              required
            />
            <Input
              label="Kilométrage"
              type="number"
              value={form.kilometrage}
              onChange={handleChange('kilometrage')}
              error={errors.kilometrage}
              min={0}
            />
          </div>
          <Input
            label="Adresse (géocodage)"
            value={form.adresse}
            onChange={handleChange('adresse')}
            placeholder="1 Rue de la Paix, Paris"
          />

          <div className="flex gap-3 pt-2">
            <Button type="submit" loading={loading}>
              {isEdit ? 'Enregistrer' : 'Créer le véhicule'}
            </Button>
            <Button type="button" variant="ghost" onClick={() => navigate('/vehicules')}>
              Annuler
            </Button>
          </div>
        </form>
      </CardBody>
    </Card>
  )
}
