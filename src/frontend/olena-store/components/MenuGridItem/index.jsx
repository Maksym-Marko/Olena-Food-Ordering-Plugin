import { __ } from '@wordpress/i18n';
import _ from 'lodash';
import { useState, useEffect } from "react";
import { useDispatch, useSelector } from "react-redux"
import { NavLink } from "react-router-dom";
import { selectMenuItem } from "@olenaStore/store/slices/menu-selection/menuSelectionSlice"
import { addToCart } from "@olenaStore/store/slices/cart/cartSlice"
import { use } from 'react';
import { getCurrencySymbol } from '@olenaStore/helpers';
import { useNavigate } from 'react-router-dom';

export const MenuGridItem = ({ item, getDetails }) => {

    const navigate = useNavigate();

    const defaultImage = window?.vajofoMainMenuLocalizer?.defaultImage;

    const [canAddToCart, setCanAddToCart] = useState(false);

    const dispatch = useDispatch();

    const handleCustomization = () => {        

        navigate(`/item/${item.id}`);
    };

    const cartItems = useSelector(state => state.cart.cartItems);

    const findSimilarItemsCount = () => {

        return cartItems.filter(_item => 
            _item.id === item.id
        ).length;
    };
    
    const similarItemsCount = findSimilarItemsCount();

    const selectedMenuItems = useSelector(state => state.menuSelection.menuItems);

    const selectedItem = selectedMenuItems.find(menuItem => menuItem.id === item.id);

    const handleAddToCart = () => {

        dispatch(selectMenuItem({ item }));
        
        setCanAddToCart(true)
    };

    useEffect(() => {

        if(selectedItem && canAddToCart) {

            setCanAddToCart(false)
            
            // Add to cart
            dispatch(addToCart({
                item: selectedItem
            }));
        }
    }, [selectedItem])

    const globalSettings = useSelector(state => state.globalSettings.settings);

    const currencySymbol = getCurrencySymbol(globalSettings);

    return (
        <div className="menu-item">
            <NavLink to={`/item/${item.id}`}>
                <img
                    src={item.thumbnail || defaultImage}
                    alt={item.title || __('Menu Item', 'olena-food-ordering')}
                    onError={(e) => {
                        if (e.target.src !== defaultImage) {
                            e.target.src = defaultImage;
                        }
                    }}
                />            
            </NavLink>
            <div className="menu-item-content">
                <div className="menu-item-title">
                    <NavLink to={`/item/${item.id}`}>
                        {item.title}
                    </NavLink>
                </div>
                <div className="menu-item-description">
                    {item.description?.replace(/\[.*?\]/g, '') || ''}
                </div>

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

                <div className="menu-item-footer">

                    <div className="menu-item-info">

                        <div className="menu-item-price">{currencySymbol}{item.price}</div>

                        {
                            similarItemsCount>0 &&
                            <div className="menu-item-amount">
                                <span className="amount-number">{similarItemsCount}</span>
                                <svg className="cart-icon" viewBox="0 0 24 24">
                                    <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/>
                                </svg>
                            </div>
                        }
                        
                    </div>

                    <div className="menu-item-buttons">

                        <button
                            type="button"
                            className="customize-menu-item"
                            onClick={handleCustomization}
                        >{ __('Customize', 'olena-food-ordering')}</button>

                        {
                            similarItemsCount===0 ?
                            <button
                                type="button"
                                className="add-to-cart"
                                onClick={handleAddToCart}
                            >{__('Add to Cart', 'olena-food-ordering')}</button> :
                            <NavLink
                                to='/cart'
                                className="view-cart-link"
                            >
                                {__('View Cart', 'olena-food-ordering')}
                            </NavLink>
                        }
                        
                    </div>

                    {window?.vajofoMainMenuLocalizer?.editMenuItemBaseUrl !== null &&
                        <div className="menu-item-edit-link">
                            <a href={window?.vajofoMainMenuLocalizer?.editMenuItemBaseUrl + '?post=' + item.id + '&action=edit'} target="_blank">
                                {__('Edit', 'olena-food-ordering')}
                            </a>
                        </div>
                    }

                </div>
            </div>
        </div>
    )
}
