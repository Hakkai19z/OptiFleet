import { describe, it, expect } from 'vitest'
import {
  validateImmatriculation,
  validateEmail,
  validateRequired,
  validateRange,
  validateAnnee,
} from './validators'

describe('validateImmatriculation', () => {
  it('accepts valid format AB-123-CD', () => {
    expect(validateImmatriculation('AB-123-CD')).toBe(true)
  })
  it('accepts all uppercase ZZ-999-ZZ', () => {
    expect(validateImmatriculation('ZZ-999-ZZ')).toBe(true)
  })
  it('rejects lowercase', () => {
    expect(validateImmatriculation('ab-123-cd')).toBe(false)
  })
  it('rejects missing dashes', () => {
    expect(validateImmatriculation('AB123CD')).toBe(false)
  })
  it('rejects wrong number of digits', () => {
    expect(validateImmatriculation('AB-1234-CD')).toBe(false)
  })
  it('rejects empty string', () => {
    expect(validateImmatriculation('')).toBe(false)
  })
})

describe('validateEmail', () => {
  it('accepts valid email', () => {
    expect(validateEmail('user@example.com')).toBe(true)
  })
  it('rejects missing @', () => {
    expect(validateEmail('userexample.com')).toBe(false)
  })
  it('rejects empty', () => {
    expect(validateEmail('')).toBe(false)
  })
})

describe('validateRequired', () => {
  it('accepts non-empty string', () => {
    expect(validateRequired('hello')).toBe(true)
  })
  it('rejects empty string', () => {
    expect(validateRequired('')).toBe(false)
  })
  it('rejects null', () => {
    expect(validateRequired(null)).toBe(false)
  })
  it('rejects whitespace only', () => {
    expect(validateRequired('   ')).toBe(false)
  })
})

describe('validateAnnee', () => {
  it('accepts current year', () => {
    expect(validateAnnee(new Date().getFullYear())).toBe(true)
  })
  it('rejects year below 1900', () => {
    expect(validateAnnee(1800)).toBe(false)
  })
})
