/**
 * Format a date to French locale
 */
export function formatDate(date, options = {}) {
  if (!date) return '—'
  return new Date(date).toLocaleDateString('fr-FR', options)
}

/**
 * Format a datetime to French locale
 */
export function formatDateTime(date) {
  if (!date) return '—'
  return new Date(date).toLocaleString('fr-FR')
}

/**
 * Format a currency amount in EUR
 */
export function formatCurrency(amount) {
  if (amount == null) return '—'
  return parseFloat(amount).toLocaleString('fr-FR', {
    style: 'currency',
    currency: 'EUR',
  })
}

/**
 * Format a kilometrage number with spaces
 */
export function formatKm(km) {
  if (km == null) return '—'
  return km.toLocaleString('fr-FR') + ' km'
}

/**
 * Returns true if a date is past
 */
export function isDatePassed(date) {
  if (!date) return false
  return new Date(date) < new Date()
}

/**
 * Returns the number of days until a date (negative if past)
 */
export function daysUntil(date) {
  if (!date) return null
  const diff = new Date(date).getTime() - new Date().getTime()
  return Math.ceil(diff / (1000 * 60 * 60 * 24))
}
