import { getCurrencySymbol } from '@olenaStore/helpers';
import { useSelector } from 'react-redux';

export const OrderItem = ({ quantity, name, price, selected_add_ons = [] }) => {
  const globalSettings = useSelector(state => state.globalSettings.settings);

  const currencySymbol = getCurrencySymbol(globalSettings);

  return (
    <div className="order-item">
      <div className="item-name">
        <span className="item-quantity">{quantity}×</span>
        {name}
        {selected_add_ons.length > 0 && (
          <div className="item-add-ons">
            {selected_add_ons.map((addOn, index) => (
              <div key={index} className="add-on-item">
                {addOn.quantity}× {addOn.name} ({currencySymbol}{addOn.price.toFixed(2)})
              </div>
            ))}
          </div>
        )}
      </div>
      <span>{currencySymbol}{price}</span>
    </div>
  )
}