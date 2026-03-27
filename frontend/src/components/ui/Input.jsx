import { useState } from 'react'

export default function Input({
  label,
  error,
  id,
  type = 'text',
  className = '',
  required,
  ...props
}) {
  const [focused, setFocused] = useState(false)
  const inputId = id || label?.toLowerCase().replace(/\s+/g, '_')

  return (
    <div className={['relative', className].join(' ')}>
      {label && (
        <label
          htmlFor={inputId}
          className={[
            'block text-sm font-medium mb-1 transition-colors',
            focused ? 'text-primary' : 'text-gray-700',
            error ? 'text-danger' : '',
          ].join(' ')}
        >
          {label}
          {required && <span className="text-danger ml-1">*</span>}
        </label>
      )}
      <input
        id={inputId}
        type={type}
        onFocus={() => setFocused(true)}
        onBlur={() => setFocused(false)}
        className={[
          'w-full rounded-lg border px-3 py-2 text-sm transition-all duration-150',
          'placeholder:text-gray-400 focus:outline-none focus:ring-2',
          error
            ? 'border-danger focus:border-danger focus:ring-danger/20'
            : 'border-gray-300 focus:border-primary focus:ring-primary/20',
          'disabled:bg-gray-50 disabled:text-gray-500 disabled:cursor-not-allowed',
        ].join(' ')}
        {...props}
      />
      {error && (
        <p className="mt-1 text-xs text-danger flex items-center gap-1">
          <svg className="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
            <path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clipRule="evenodd" />
          </svg>
          {error}
        </p>
      )}
    </div>
  )
}
