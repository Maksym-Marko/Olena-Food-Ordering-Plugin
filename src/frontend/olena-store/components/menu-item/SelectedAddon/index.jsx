import { useState, useEffect } from 'react';
import { getCurrencySymbol } from '@olenaStore/helpers';
import { useSelector } from 'react-redux';

export const SelectedAddon = ({ addon, unselectAddon, changeQuantity }) => {

    const globalSettings = useSelector(state => state.globalSettings.settings);

    const currencySymbol = getCurrencySymbol(globalSettings);

    const [quantity, setQuantity] = useState(1);

    useEffect(() => {

        setQuantity(addon.quantity || 1);
    }, []);

    const handleRemoveFromSelected = () => {        

        unselectAddon(addon)
    }

    useEffect(() => {

        const updatedAddon = {
            ...addon,
            quantity
        };

        changeQuantity(updatedAddon);

    }, [quantity])

    return (
        <div 
            className="selected-addon"
            id={`selected-add-on-id-${addon.id}`}
        >
            <div className="selected-addon-left">
                <span className="addon-name">{addon.name}</span>
                <div className="quantity-selector">
                    <button
                        className="quantity-btn"
                        onClick={() => setQuantity(Math.max(1, quantity - 1))}
                        disabled={quantity === 1}
                    >−</button>
                    <span className="quantity-value">{quantity}</span>
                    <button
                        className="quantity-btn"
                        onClick={() => setQuantity(prevQ => prevQ<parseInt(addon.max)?prevQ+1:prevQ)}
                        disabled={quantity === addon.max}
                    >+</button>
                </div>
            </div>
            <div className="selected-addon-right">
                <span className="addon-price">
                    {currencySymbol}{addon.price}
                    {
                        quantity>1 && <>
                            /{(parseFloat(addon.price) * quantity).toFixed(2)}
                        </>
                    }
                </span>
                <button 
                    type="button"
                    className="delete-btn"
                    onClick={handleRemoveFromSelected}
                >×</button>
            </div>
        </div>
    )
}