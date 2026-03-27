import { render, screen } from '@testing-library/react'
import { describe, it, expect } from 'vitest'
import Badge from './Badge'

describe('Badge', () => {
  it('renders disponible badge with correct label', () => {
    render(<Badge value="disponible" />)
    expect(screen.getByText('Disponible')).toBeInTheDocument()
  })

  it('renders en_mission badge', () => {
    render(<Badge value="en_mission" />)
    expect(screen.getByText('En mission')).toBeInTheDocument()
  })

  it('renders maintenance badge', () => {
    render(<Badge value="maintenance" />)
    expect(screen.getByText('Maintenance')).toBeInTheDocument()
  })

  it('renders inactif badge', () => {
    render(<Badge value="inactif" />)
    expect(screen.getByText('Inactif')).toBeInTheDocument()
  })

  it('renders alerte badge', () => {
    render(<Badge value="alerte" />)
    expect(screen.getByText('Alerte')).toBeInTheDocument()
  })

  it('renders custom children', () => {
    render(<Badge value="disponible">Custom label</Badge>)
    expect(screen.getByText('Custom label')).toBeInTheDocument()
  })

  it('renders unknown value with default style', () => {
    render(<Badge value="unknown_value" />)
    expect(screen.getByText('unknown_value')).toBeInTheDocument()
  })

  it('applies correct CSS class for disponible', () => {
    const { container } = render(<Badge value="disponible" />)
    expect(container.firstChild).toHaveClass('bg-green-100')
  })

  it('applies correct CSS class for alerte', () => {
    const { container } = render(<Badge value="alerte" />)
    expect(container.firstChild).toHaveClass('bg-red-100')
  })
})
