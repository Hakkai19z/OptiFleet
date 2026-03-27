const accentColors = {
  primary: 'border-l-primary',
  blue:    'border-l-blue-fleet',
  teal:    'border-l-teal-fleet',
  amber:   'border-l-amber-fleet',
  danger:  'border-l-danger',
  green:   'border-l-green-500',
}

export default function Card({ children, accent, className = '', ...props }) {
  return (
    <div
      className={[
        'bg-white rounded-xl shadow-sm border border-gray-100',
        accent ? `border-l-4 ${accentColors[accent] || accentColors.primary}` : '',
        className,
      ].join(' ')}
      {...props}
    >
      {children}
    </div>
  )
}

export function CardHeader({ children, className = '' }) {
  return (
    <div className={['px-6 py-4 border-b border-gray-100', className].join(' ')}>
      {children}
    </div>
  )
}

export function CardBody({ children, className = '' }) {
  return (
    <div className={['px-6 py-4', className].join(' ')}>
      {children}
    </div>
  )
}

export function CardFooter({ children, className = '' }) {
  return (
    <div className={['px-6 py-4 border-t border-gray-100 bg-gray-50 rounded-b-xl', className].join(' ')}>
      {children}
    </div>
  )
}
