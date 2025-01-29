export const Button = ({ type = 'button', to, className = '', variant = 'primary', children, ...props }) => {
  const buttonClass = `fo-btn fo-btn_${variant} ${className}`;

  return (
    <button type={type} className={buttonClass} {...props}>
      {children}
    </button>
  );
};