export const FormActions = ({ className = '', children }) => {
  return (
    <div className={`fo-form-actions ${className}`}>
      {children}
    </div>
  );
};
