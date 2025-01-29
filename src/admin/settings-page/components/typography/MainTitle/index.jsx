export const MainTitle = ({ as: Component = 'h1', className = '', children }) => {
  return (
    <Component className={`fo-main-title ${className}`}>
      {children}
    </Component>
  );
};