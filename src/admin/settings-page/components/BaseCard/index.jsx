export const BaseCard = ({ className = '', children }) => {
  return (
    <div className={`fo-base-card ${className}`}>
      {children}
    </div>
  );
};

