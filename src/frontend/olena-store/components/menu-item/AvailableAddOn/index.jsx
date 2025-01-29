import { getCurrencySymbol } from '@olenaStore/helpers';
import { useSelector } from 'react-redux';

export const AvailableAddOn = ({addon, selectAddon}) => {

    const globalSettings = useSelector(state => state.globalSettings.settings);

    const currencySymbol = getCurrencySymbol(globalSettings);

    const handleSelectAddOn = () => {

        selectAddon(addon)
    }

    return (
        <div 
            className="addon-item"
            onClick={handleSelectAddOn}
            id={`add-on-id-${addon.id}`}
        >
            <span className="addon-name">{addon.name}</span>
            <span className="addon-price">{currencySymbol}{addon.price}</span>
        </div>
    )
}