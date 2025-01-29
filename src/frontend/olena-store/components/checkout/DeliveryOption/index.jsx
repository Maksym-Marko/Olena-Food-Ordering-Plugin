import { getCurrencySymbol } from '@olenaStore/helpers';
import { useSelector } from 'react-redux';

export const DeliveryOption = ({ title, description, price, isSelected, onChange, disabled }) => {
    const globalSettings = useSelector(state => state.globalSettings.settings);

    const currencySymbol = getCurrencySymbol(globalSettings);

    const deliveryId = `delivery-${title.toLowerCase().replace(/\s+/g, '-')}`;
    return (
      <label 
        htmlFor={deliveryId} 
        className={`delivery-option ${isSelected ? 'selected' : ''} ${disabled ? 'ofo-option-disabled' : ''}`}
      >
        <input
          id={deliveryId}
          type="radio"
          name="delivery"
          className="delivery-radio"
          checked={isSelected}
          onChange={onChange}
          disabled={disabled}
        />
        <div className="delivery-details">
          <div className="delivery-title">{title}</div>
          <div className="delivery-description">{description}</div>
        </div>
        <div className="delivery-price">{currencySymbol}{price}</div>
      </label>
    );
};