import { getCurrencySymbol } from "@olenaStore/helpers";
import { useSelector } from "react-redux";

export const OrderSummary = ({ summary }) => {
    const settings = useSelector(state => state.globalSettings.settings);
    const currencySymbol = getCurrencySymbol(settings);

    return (
        <div className="order-summary">
            {Object.entries(summary).map(([key, value]) => (
                key === 'total' ? (
                    <div key={key} className="total-amount">
                        <span>Total</span>
                        <span>{currencySymbol}{value.toFixed(2)}</span>
                    </div>
                ) : (
                    <div key={key} className="summary-row">
                        <span>{key}</span>
                        <span>{currencySymbol}{value.toFixed(2)}</span>
                    </div>
                )
            ))}
        </div>
    );
};