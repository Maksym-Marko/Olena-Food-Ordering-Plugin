import { __ } from '@wordpress/i18n';
import React, { useState } from 'react';
import { useDispatch } from "react-redux"
import { setSelectedAddOn } from '@addOnsManager/store/slices/addOns/addOnsManagerSlice';

export const AddOnSelector = ({
    addOnsCategoryId,
    onSelect,
    selectedAddons = {},
    availableAddons = {}
}) => {

    const hasAllAddOns = () => {

        return Object.keys(availableAddons).every(key => key in selectedAddons);
    };

    const [selectedValue, setSelectedValue] = useState('');

    const dispatch = useDispatch();

    const handleChange = (e) => {

        const addOnId = e.target.value;
        
        if (addOnId) {
            dispatch(setSelectedAddOn({categoryId: addOnsCategoryId, addOnId}));
        }
    };

    const getOptionLabel = (addon) => {
        return `${addon.name} - $${parseFloat(addon.price).toFixed(2)}`;
    };

    return (
        !hasAllAddOns() && (
            <div className="category-settings">
                <div className="addon-selector">
                    <select
                        className="addon-select"
                        value={selectedValue}
                        onChange={handleChange}
                    >
                        <option value="" disabled>
                            {__('+ Add addon to this category', 'olena-food-ordering')}
                        </option>

                        {Object.entries(availableAddons)
                            .filter(([id]) => !selectedAddons.hasOwnProperty(id))
                            .map(([id, addon]) => (
                                <option
                                    key={id}
                                    value={id}
                                >
                                    {getOptionLabel(addon)}
                                </option>
                            ))
                        }

                    </select>
                </div>
            </div>
        )
    );
};
