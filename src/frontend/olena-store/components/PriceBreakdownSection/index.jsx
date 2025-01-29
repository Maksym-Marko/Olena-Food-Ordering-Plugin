import { __ } from '@wordpress/i18n';
import { useSelector } from "react-redux"
import { useState, useEffect } from "react";
import { getCurrencySymbol } from '@olenaStore/helpers';

export const PriceBreakdownSection = ({ item }) => {

    const [currentItem, setCurrentItem] = useState(null);
    const [itemPrice, setItemPrice] = useState(0);
    const [addOnsPrice, setAddOnsPrice] = useState(0);
    const [fullPrice, setFullPrice] = useState(0);

    const selectedMenuItems = useSelector(state => state.menuSelection.menuItems);

    useEffect(() => {

        const existingItemIndex = selectedMenuItems.findIndex(

            selection => selection.id === item.id
        );

        if (existingItemIndex === -1) return

        setCurrentItem(selectedMenuItems[existingItemIndex])
    }, [selectedMenuItems]);

    useEffect(() => {

        if (!currentItem) return

        getItemPrice()
        calculateAddOnsPrice()
        calculateFullPrice()

    }, [currentItem]);

    useEffect(() => {

        calculateFullPrice()
    }, [itemPrice, addOnsPrice]);

    const getItemPrice = () => {

        setItemPrice(parseFloat(currentItem.price_per_unit || 0).toFixed(2))
    };

    const calculateAddOnsPrice = () => {

        let currentAddOnsPrice = 0;

        currentItem.selected_add_ons.forEach(addOn => {

            const addOnPrice = parseFloat(addOn.price) * parseInt(addOn.quantity);

            currentAddOnsPrice += addOnPrice;
        })

        setAddOnsPrice(parseFloat(currentAddOnsPrice || 0).toFixed(2))
    };

    const calculateFullPrice = () => {

        setFullPrice(parseFloat(parseFloat(itemPrice || 0) + parseFloat(addOnsPrice || 0)).toFixed(2))
    };

    const globalSettings = useSelector(state => state.globalSettings.settings);

    const currencySymbol = getCurrencySymbol(globalSettings);

    return (
        <div className="total-section">
            <div className="total-row">
                <span>{__('Base price:', 'olena-food-ordering')}</span>
                <span>{currencySymbol}{itemPrice}</span>
            </div>
            <div className="total-row">
                <span>{__('Add-ons:', 'olena-food-ordering')}</span>
                <span>{currencySymbol}{addOnsPrice}</span>
            </div>
            <div className="total-row final-total">
                <span>{__('Total:', 'olena-food-ordering')}</span>
                <span>{currencySymbol}{fullPrice}</span>
            </div>
        </div>
    )
}