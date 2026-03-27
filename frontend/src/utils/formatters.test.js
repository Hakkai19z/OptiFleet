import { describe, it, expect } from 'vitest'
import { formatDate, formatCurrency, formatKm, isDatePassed, daysUntil } from './formatters'

describe('formatDate', () => {
  it('returns — for null', () => {
    expect(formatDate(null)).toBe('—')
  })
  it('formats a valid date', () => {
    const result = formatDate('2024-01-15')
    expect(result).toMatch(/\d{2}\/\d{2}\/\d{4}/)
  })
})

describe('formatCurrency', () => {
  it('returns — for null', () => {
    expect(formatCurrency(null)).toBe('—')
  })
  it('formats a number as EUR', () => {
    const result = formatCurrency(1500)
    expect(result).toContain('1')
    expect(result).toContain('500')
    expect(result).toContain('€')
  })
})

describe('formatKm', () => {
  it('returns — for null', () => {
    expect(formatKm(null)).toBe('—')
  })
  it('formats with km suffix', () => {
    expect(formatKm(45000)).toContain('km')
  })
})

describe('isDatePassed', () => {
  it('returns false for null', () => {
    expect(isDatePassed(null)).toBe(false)
  })
  it('returns true for past date', () => {
    expect(isDatePassed('2000-01-01')).toBe(true)
  })
  it('returns false for future date', () => {
    const future = new Date()
    future.setFullYear(future.getFullYear() + 1)
    expect(isDatePassed(future.toISOString())).toBe(false)
  })
})

describe('daysUntil', () => {
  it('returns null for null', () => {
    expect(daysUntil(null)).toBe(null)
  })
  it('returns negative for past date', () => {
    expect(daysUntil('2000-01-01')).toBeLessThan(0)
  })
  it('returns positive for future date', () => {
    const future = new Date()
    future.setDate(future.getDate() + 10)
    expect(daysUntil(future.toISOString())).toBeGreaterThan(0)
  })
})
