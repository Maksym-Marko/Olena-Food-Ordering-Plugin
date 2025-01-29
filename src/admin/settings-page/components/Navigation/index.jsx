import { NavLink } from "react-router-dom";
import clsx from 'clsx';

export const Navigation = ({ navigation }) => {
  return (
    <div className="fo-admin-header">
      <nav className="fo-tabs">
        {navigation.map((item) => (
          <NavLink
            key={item.name}
            to={item.path}
            className={({ isActive }) =>
              clsx('fo-tabs__item', isActive && 'fo-tabs__item_active')
            }
          >
            {item.label}
          </NavLink>
        ))}
      </nav>
    </div>
  );
};

