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
  const id = type === 'checkbox' || type === 'radio' ? false : `field-${name}`;

  return (
    <div className={`fo-field ${error ? 'fo-field_error' : ''}`}>
      {label && (
        <label 
          htmlFor={id || undefined} 
          className="fo-field__label"
        >
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
      ) : type === 'radio' ? (
        <div className="fo-field__radio-group">
          {props.options && props.options.map((option) => (
            <label key={`${name}-${option.value}`} htmlFor={`${name}-${option.value}`} className="fo-field__radio-label">
              <input
                id={`${name}-${option.value}`}
                type={type}
                name={name}
                className="fo-field__radio"
                checked={value === option.value}
                value={option.value}
                onChange={onChange}
                aria-invalid={!!error}
              />
              {option.label}
            </label>
          ))}
        </div>
      ) : type === 'checkbox' ? (
        <div className="fo-field__checkbox-group">
          {props.options && props.options.map((option) => (
            <label key={`${name}-${option.value}`} htmlFor={`${name}-${option.value}`} className="fo-field__checkbox-label">
              
              <input
                id={`${name}-${option.value}`}
                type={type}
                name={name}
                className="fo-field__checkbox"
                checked={JSON.parse(value || '{}')?.[option.value] || false}
                value={option.value}
                onChange={(e) => {
                  let currentValue = value;
                  try {
                    if (typeof value === 'string') {
                      currentValue = JSON.parse(value);
                    }
                  } catch (e) {
                    // Keep original value if parsing fails
                  }
                  const newValue = {
                    ...currentValue,
                    [option.value]: e.target.checked
                  };
                  
                  onChange({
                    target: {
                      name,
                      value: JSON.stringify(newValue)
                    }
                  });
                }}
                aria-invalid={!!error}
              />
              {option.label}
            </label>
          ))}
        </div>
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
