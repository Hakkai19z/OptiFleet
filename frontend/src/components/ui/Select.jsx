export default function Select({ label, options, error, id, className = '', required, ...props }) {
  const selectId = id || label?.toLowerCase().replace(/\s+/g, '_')

  return (
    <div className={['space-y-1', className].join(' ')}>
      {label && (
        <label htmlFor={selectId} className="block text-sm font-medium text-gray-700">
          {label}
          {required && <span className="text-danger ml-1">*</span>}
        </label>
      )}
      <select
        id={selectId}
        className={[
          'w-full rounded-lg border px-3 py-2 text-sm transition-all duration-150 bg-white',
          'focus:outline-none focus:ring-2',
          error
            ? 'border-danger focus:border-danger focus:ring-danger/20'
            : 'border-gray-300 focus:border-primary focus:ring-primary/20',
          'disabled:bg-gray-50 disabled:text-gray-500 disabled:cursor-not-allowed',
        ].join(' ')}
        {...props}
      >
        {options.map((opt) => (
          <option key={opt.value} value={opt.value}>
            {opt.label}
          </option>
        ))}
      </select>
      {error && <p className="text-xs text-danger">{error}</p>}
    </div>
  )
}
