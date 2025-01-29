import { getCurrencySymbol } from "@orderDetails/helpers";
import { useSelector } from "react-redux";

export const OrderItem = ({ name, quantity, price_per_unit, selected_add_ons }) => {

    const settings = useSelector(state => state.globalSettings.settings);
    const currencySymbol = getCurrencySymbol(settings);

    const addonsTotal = selected_add_ons?.reduce((sum, addon) => 
        sum + (addon.price * addon.quantity), 0) || 0;
    const totalPrice = (price_per_unit * quantity) + addonsTotal;

    return (
        <div className="order-item">
            <div className="item-details">
                <div className="item-name">{name} × {quantity}</div>
                {selected_add_ons && selected_add_ons.length > 0 && (
                    <div className="item-addons">
                        {selected_add_ons.map((addon, index) => (
                            <div key={index}>+ {addon.name} × {addon.quantity} ({currencySymbol}{addon.price.toFixed(2)})</div>
                        ))}
                    </div>
                )}
            </div>
            <div className="item-price">{currencySymbol}{totalPrice.toFixed(2)}</div>
        </div>
    );
};