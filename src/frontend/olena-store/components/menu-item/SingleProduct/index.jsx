import { __ } from '@wordpress/i18n';
import _ from 'lodash';
import { SelectedAddon } from '@olenaStore/components/menu-item/SelectedAddon'
import { AvailableAddOnCategory } from '@olenaStore/components/menu-item/AvailableAddOnCategory'
import { PriceBreakdownSection } from '@olenaStore/components/PriceBreakdownSection'
import { useDispatch, useSelector } from "react-redux"
import { deleteAddOn, modifyAddOn, emptyAddOns, selectMenuItem } from "@olenaStore/store/slices/menu-selection/menuSelectionSlice"
import { addToCart } from "@olenaStore/store/slices/cart/cartSlice"
import { DishDescription } from '@olenaStore/components/menu-item/DishDescription'
import { useEffect, useRef } from 'react';
import { NavLink } from "react-router-dom";
import { getCurrencySymbol } from '@olenaStore/helpers';

export const SingleProduct = ({ availableAddOns, item, handleClose }) => {

    // add to selection
    useEffect(() => {

        if(item) {

            dispatch(selectMenuItem({ item }));
        }
    }, [item]);

    const containerRef = useRef(null);

    useEffect(() => {

        const handleClickOutside = (event) => {

            if (containerRef.current && !containerRef.current.contains(event.target)) {

                handleClose();
            }
        };

        document.addEventListener('mousedown', handleClickOutside);

        return () => {
            document.removeEventListener('mousedown', handleClickOutside);
        };
    }, []);

    const defaultImage = window?.vajofoMainMenuLocalizer?.defaultImage;

    const selectedMenuItems = useSelector(state => state.menuSelection.menuItems);

    const selectedItem = selectedMenuItems.find(menuItem => menuItem.id === item.id);

    const dispatch = useDispatch();

    const filteredAddOns = Object.keys(item.add_ons)
        .filter(key => availableAddOns.hasOwnProperty(key))
        .reduce((acc, key) => {
            acc[key] = availableAddOns[key];
            return acc;
        }, {});

    const handleUnselectAddon = (addon) => {

        dispatch(deleteAddOn({
            addon,
            menuItem: item
        }));
    };

    const handleChangeQuantity = (addon) => {

        dispatch(modifyAddOn({
            addon,
            menuItem: item
        }));
    };

    const handleAddToCart = (e) => {

        e.preventDefault();

        dispatch(addToCart({
            item: selectedItem
        }));

        // ???
        // dispatch(emptyAddOns({
        //     itemId: selectedItem.id
        // }));
    };

    const cartItems = useSelector(state => state.cart.cartItems);

    const findSimilarItemInCart = () => {

        if (!selectedItem) return;

        return cartItems.some(item =>
            item.id === selectedItem.id && _.isEqual(item?.selected_add_ons, selectedItem?.selected_add_ons)
        );
    };

    const hasSimilarItem = findSimilarItemInCart();

    const globalSettings = useSelector(state => state.globalSettings.settings);

    const currencySymbol = getCurrencySymbol(globalSettings);

    return (
        <div className="ofo-single-product-popup">
            <div
                ref={containerRef}
                className="ofo-container"
            >
                <div className="main-content-wrapper">
                    {/* Left Column - Dish Details */}
                    <div className="dish-details">
                        <div className="dish-header">
                            <div className="title-wrapper">
                                <h1 className="dish-title">{item.title}</h1>
                                {/* <button className="favorite-btn">
                                    <svg className="heart-icon" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" strokeWidth="2">
                                        <path
                                            d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                                    </svg>
                                </button> */}
                            </div>
                        </div>

                        <img
                            src={item.thumbnail || defaultImage}
                            alt={item.title || __('Menu Item', 'olena-food-ordering')}
                            onError={(e) => {
                                if (e.target.src !== defaultImage) {
                                    e.target.src = defaultImage;
                                }
                            }}
                        />

                        {
                            ((Array.isArray(item?.categories) && item.categories.length > 0) ||
                                (Array.isArray(item?.tags) && item.tags.length > 0)) &&
                            <div className="menu-item-meta">
                                {
                                    (Array.isArray(item?.categories) && item.categories.length > 0) &&
                                    <div className="menu-item-categories">
                                        {item.categories.map(category => (
                                            <a
                                                key={category.id}
                                                href={`#${category.slug}`}
                                                onClick={(e) => {
                                                    e.preventDefault();
                                                    return false;
                                                }}
                                            >
                                                {category.name}
                                            </a>
                                        ))}
                                    </div>
                                }

                                {
                                    (Array.isArray(item?.tags) && item.tags.length > 0) &&
                                    <div className="menu-item-tags">
                                        {item.tags.map(tag => (
                                            <a
                                                key={tag.id}
                                                href={`#${tag.slug}`}
                                                onClick={(e) => {
                                                    e.preventDefault();
                                                    return false;
                                                }}
                                            >
                                                {tag.name}
                                            </a>
                                        ))}
                                    </div>
                                }
                            </div>
                        }

                        <div className="dish-description">
                            <DishDescription
                                description={item.description}
                            />
                        </div>

                        <div className="price-tag">{currencySymbol}{item.price}</div>

                        {
                            (Object.keys(filteredAddOns || {}).length > 0) &&
                            <>
                                <h3 className="customization-title">{__('Your Customizations', 'olena-food-ordering')}</h3>

                                <div className="customization-area">

                                    {
                                        selectedItem?.selected_add_ons && selectedItem.selected_add_ons.length > 0 &&
                                        selectedItem.selected_add_ons.map(addon => (
                                            <SelectedAddon
                                                key={addon.id}
                                                addon={addon}
                                                unselectAddon={handleUnselectAddon}
                                                changeQuantity={handleChangeQuantity}
                                            />
                                        ))
                                    }

                                </div>
                            </>
                        }

                        <PriceBreakdownSection
                            item={item}
                        />

                        {/* Coupon area */}

                        {
                            !hasSimilarItem ?
                                <button
                                    className="order-button"
                                    onClick={handleAddToCart}
                                >{__('Add to Cart', 'olena-food-ordering')}</button> :
                                <div className="item-already-in-the-cart">{__('This item is already in your cart - you can adjust its quantity on the cart page or select different add-ons to add it as new.', 'olena-food-ordering')}</div>
                        }

                        {
                            cartItems.length > 0 &&
                            <NavLink
                                key={item.name}
                                to='/cart'
                                className="order-button ofo-in-cart"
                            >
                                {__('View Cart', 'olena-food-ordering')} ({cartItems.length})
                            </NavLink>
                        }

                        {window?.vajofoMainMenuLocalizer?.editMenuItemBaseUrl !== null &&
                            <div className="menu-item-edit-link">
                                <a href={window?.vajofoMainMenuLocalizer?.editMenuItemBaseUrl + '?post=' + item.id + '&action=edit'} target="_blank">
                                    {__('Edit', 'olena-food-ordering')}
                                </a>
                            </div>
                        }

                    </div>

                    {/* Right Column - Addons List */}
                    <div className="addons-list">

                        <button
                            type="button"
                            className="popup-close-btn"
                            aria-label="Close popup"
                            onClick={handleClose}
                        >
                            <svg viewBox="0 0 24 24">
                                <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z" />
                            </svg>
                        </button>

                        <h2 className="addons-title">{__('Available Add-ons', 'olena-food-ordering')}</h2>

                        <div>

                            {
                                (Object.keys(filteredAddOns || {}).length === 0) && <div className="ofo-no-add-ons-heading">{__('No add-ons available', 'olena-food-ordering')}</div>
                            }

                            {filteredAddOns && Object.entries(filteredAddOns).map(([categoryId, categoryData]) => (
                                <AvailableAddOnCategory
                                    key={categoryId}
                                    categoryId={categoryId}
                                    categoryData={categoryData}
                                    menuItem={item}
                                />
                            ))}

                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};
