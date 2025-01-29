import { __ } from '@wordpress/i18n';
import { AvailableAddOn } from '@olenaStore/components/menu-item/AvailableAddOn'
import { modifyAddOn } from "@olenaStore/store/slices/menu-selection/menuSelectionSlice"
import { useDispatch, useSelector } from "react-redux"
import { useEffect, useState } from 'react';

export const AvailableAddOnCategory = ({ categoryId, categoryData, menuItem }) => {

    const [selectedAddOns, setSelectedAddOns] = useState([]);

    const selectedMenuItems = useSelector(state => state.menuSelection.menuItems);

    useEffect(() => {

        getSelectedAddOns()        
    }, [selectedMenuItems]);

    const getSelectedAddOns = () => {

        if(!Array.isArray(selectedMenuItems) || selectedMenuItems.length === 0) return

        const currentItem = selectedMenuItems.find(element => element.id === menuItem.id)

        if(!currentItem?.selected_add_ons) return

        setSelectedAddOns(currentItem.selected_add_ons);
    };

    const dispatch = useDispatch();
    
    const itemAddOns = menuItem.add_ons;
    
    if (!categoryId || !categoryData || !itemAddOns || !itemAddOns[categoryId]) {
        return null;
    }

    const createAddons = (categoryId, categoryData, itemAddOns) => {
        return Object.keys(itemAddOns[categoryId])
            .filter(key => categoryData.add_ons?.hasOwnProperty(key))
            .filter(key => !selectedAddOns.some(selectedAddOn => parseInt(selectedAddOn.id) === parseInt(key)))        
            .map(key => ({
                name: categoryData.add_ons[key].name,
                price: categoryData.add_ons[key].price,
                min: itemAddOns[categoryId][key].min,
                max: itemAddOns[categoryId][key].max,
                id: parseInt(key)
            }));
    };

    const addons = createAddons(categoryId, categoryData, itemAddOns);

    const handleSelectAddOn = (addon) => {

        dispatch(modifyAddOn({
            addon,
            menuItem
        }));
    }

    return (
        <div
            className="ofo-available-add-ons-category"
            id={`ofo-add-on-cat-${categoryId}`}
        >
            <div className="ofo-available-add-ons-category-name">
                {categoryData?.name}
            </div>

            {
                addons.length>0 ?
                addons.map((addon, index) => (
                    <AvailableAddOn
                        key={`${categoryId}-${index}`}
                        addon={addon}
                        selectAddon={handleSelectAddOn}
                    />
                )) :
                <div className="ofo-available-add-ons-category-no-add-ons-more">{__('No add-ons more', 'olena-food-ordering')}</div>
            }

        </div>
    )
}