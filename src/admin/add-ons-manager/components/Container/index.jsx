export const Container = ({ className = '', children }) => {
  return (
    <div className={`${className}`}>
      {children}
    </div>
  );
};
