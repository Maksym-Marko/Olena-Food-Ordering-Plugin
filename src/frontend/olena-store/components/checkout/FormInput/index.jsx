export const FormInput = ({ label, type, required, fullWidth, value, onChange, hasError }) => (
    <div className={`form-group ${fullWidth ? 'form-full' : ''}`}>
      <label className="form-label">{label}</label>
      <input
        type={type}
        className={`form-input ${hasError ? 'invalid-fields' : ''}`}
        required={required}
        value={value}
        onChange={onChange}
      />
    </div>
);