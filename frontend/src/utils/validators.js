/**
 * Validates French vehicle plate: AA-000-AA
 */
export function validateImmatriculation(value) {
  return /^[A-Z]{2}-[0-9]{3}-[A-Z]{2}$/.test(value)
}

/**
 * Validates an email address
 */
export function validateEmail(value) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)
}

/**
 * Checks if a value is not empty
 */
export function validateRequired(value) {
  if (value === null || value === undefined) return false
  return String(value).trim().length > 0
}

/**
 * Checks if a number is within a range
 */
export function validateRange(value, min, max) {
  const n = Number(value)
  if (isNaN(n)) return false
  if (min !== undefined && n < min) return false
  if (max !== undefined && n > max) return false
  return true
}

/**
 * Validates a year (1900 - current year + 2)
 */
export function validateAnnee(value) {
  return validateRange(value, 1900, new Date().getFullYear() + 2)
}
