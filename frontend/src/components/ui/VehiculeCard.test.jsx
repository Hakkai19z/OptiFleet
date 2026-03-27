import { render, screen } from '@testing-library/react'
import { MemoryRouter } from 'react-router-dom'
import { describe, it, expect } from 'vitest'
import VehiculeCard from './VehiculeCard'

const mockVehicule = {
  id: 1,
  immatriculation: 'AB-123-CD',
  marque: 'Renault',
  modele: 'Clio',
  annee: 2021,
  kilometrage: 45000,
  statut: 'disponible',
  categorie: { libelle: 'Citadine' },
}

describe('VehiculeCard', () => {
  const render_ = (v = mockVehicule) =>
    render(
      <MemoryRouter>
        <VehiculeCard vehicule={v} />
      </MemoryRouter>
    )

  it('renders vehicle name', () => {
    render_()
    expect(screen.getByText('Renault Clio')).toBeInTheDocument()
  })

  it('renders immatriculation', () => {
    render_()
    expect(screen.getByText('AB-123-CD')).toBeInTheDocument()
  })

  it('renders status badge', () => {
    render_()
    expect(screen.getByText('Disponible')).toBeInTheDocument()
  })

  it('renders kilometrage formatted', () => {
    render_()
    expect(screen.getByText(/45\s?000 km/)).toBeInTheDocument()
  })

  it('renders categorie', () => {
    render_()
    expect(screen.getByText('Citadine')).toBeInTheDocument()
  })

  it('renders detail link', () => {
    render_()
    expect(screen.getByRole('link', { name: /voir le détail/i })).toHaveAttribute('href', '/vehicules/1')
  })

  it('renders without categorie gracefully', () => {
    render_({ ...mockVehicule, categorie: null })
    expect(screen.getByText('Renault Clio')).toBeInTheDocument()
  })

  it('renders maintenance badge for maintenance status', () => {
    render_({ ...mockVehicule, statut: 'maintenance' })
    expect(screen.getByText('Maintenance')).toBeInTheDocument()
  })
})
