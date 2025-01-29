import React from 'react';

export const FormField = ({
  label,
  type = 'text',
  name,
  description,
  value,
  onChange,
  error,
  ...props
}) => {
  const id = `field-${name}`;

  return (
    <div className={`fo-field ${error ? 'fo-field_error' : ''}`}>
      {label && (
        <label htmlFor={id} className="fo-field__label">
          {label}
        </label>
      )}

      {type === 'textarea' ? (
        <textarea
          id={id}
          name={name}
          className="fo-field__textarea"
          value={value}
          onChange={onChange}
          aria-invalid={!!error}
          {...props}
        />
      ) : type === 'select' ? (
        <select
          id={id}
          name={name}
          className="fo-field__select"
          value={value}
          onChange={onChange}
          aria-invalid={!!error}
          {...props}
        >
          {props.options && props.options.map((option) => (
            <option key={option.value} value={option.value}>
              {option.label}
            </option>
          ))}
        </select>
      ) : (
        <input
          id={id}
          type={type}
          name={name}
          className="fo-field__input"
          value={value}
          onChange={onChange}
          aria-invalid={!!error}
          {...props}
        />
      )}

      {error && <div className="fo-field__error">{error}</div>}
      {description && <div className="fo-field__desc">{description}</div>}
    </div>
  );
};
