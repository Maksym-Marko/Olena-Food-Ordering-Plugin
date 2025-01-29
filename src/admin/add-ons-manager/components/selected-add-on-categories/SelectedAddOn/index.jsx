import { __ } from '@wordpress/i18n';
import React, { useEffect, useState } from 'react';
import { setMinNumber, setMaxNumber, removeSelectedAddon } from '@addOnsManager/store/slices/addOns/addOnsManagerSlice';
import { useDispatch, useSelector } from "react-redux"
import { getCurrencySymbol } from "@addOnsManager/helpers"

export const SelectedAddOn = ({
    categoryId = 0,
    addOnId = 0,
    className = '',
    children,
    addonName = 'Lemon Butter Sauce',
    addonPrice = '2.99'
}) => {
    const [minValue, setMinValue] = useState(0);
    const [maxValue, setMaxValue] = useState(10);

    const dispatch = useDispatch();

    const manageMinValue = (newMin) => {

        if (newMin >= 0 && newMin < maxValue) {

            return parseInt(newMin);
        }

        return 0
    };

    const manageMaxValue = (newMax) => {

        if (newMax >= minValue && newMax <= 10) {
            
            return parseInt(newMax);
        }
        return maxValue;
    };

    const handleMinChange = (e) => {

        const newMin = parseInt(e.target.value);

        setMinValue(manageMinValue(newMin));

        dispatch(setMinNumber({ categoryId, addOnId, newMin }))
    };

    const handleMaxChange = (e) => {

        const newMax = parseInt(e.target.value);

        setMaxValue(manageMaxValue(newMax));

        dispatch(setMaxNumber({ categoryId, addOnId, newMax }))
    };

    const selectedAddons = useSelector(state => state.addOnsManager.selectedAddons)

    const setAddOnMinNumber = () => {

        const newMin = selectedAddons?.[categoryId]?.[addOnId]?.min ?? 0

        setMinValue(manageMinValue(newMin));
    }

    const setAddOnMaxNumber = () => {

        const newMax = selectedAddons?.[categoryId]?.[addOnId]?.max ?? 0

        setMaxValue(manageMaxValue(newMax));
    }

    useEffect(() => {

        if (Object.keys(selectedAddons).length > 0) {

            setAddOnMinNumber();
            setAddOnMaxNumber();
        }
    }, [selectedAddons])

    const handleAddOnDelete = () => {

        dispatch(removeSelectedAddon({ categoryId, addOnId }))
    }

    const settings = useSelector(state => state.globalSettings.settings);
    const currencySymbol = getCurrencySymbol(settings);

    return (
        <div
            className={`addon-item ${className}`}
            id={`selected-add-on-${addOnId}`}
            key={`selected-add-on-${addOnId}`}
        >
            <div className="addon-info">
                <span className="addon-name">{addonName}</span>
                <span className="addon-price">{currencySymbol}{addonPrice}</span>
            </div>
            <div className="addon-controls">
                <div className="addon-limits">
                    <label className="limit-label">
                        {__('Min:', 'olena-food-ordering')}
                        <input
                            type="number"
                            value={minValue}
                            onChange={handleMinChange}
                            min="0"
                            max="10"
                            className="limit-input"
                        />
                    </label>
                    <label className="limit-label">
                        {__('Max:', 'olena-food-ordering')}
                        <input
                            type="number"
                            value={maxValue}
                            onChange={handleMaxChange}
                            min="0"
                            max="10"
                            className="limit-input"
                        />
                    </label>
                </div>
                <button
                    className="cancel-addon-btn"
                    title={__('Remove From Selected', 'olena-food-ordering')}
                    onClick={handleAddOnDelete}
                >
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M18 6L6 18M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>
    );
};