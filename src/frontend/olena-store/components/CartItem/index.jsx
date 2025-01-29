import { __ } from '@wordpress/i18n'
import { DishDescription } from '@olenaStore/components/menu-item/DishDescription';
import { useState, useEffect } from 'react';
import { useDispatch } from 'react-redux';
import { deleteFromCart, updateCartItemQuantity } from "@olenaStore/store/slices/cart/cartSlice"
import { getCurrencySymbol } from '@olenaStore/helpers';
import { useSelector } from 'react-redux';
import { NavLink } from 'react-router-dom';

export const CartItem = ({ item, index }) => {

    const dispatch = useDispatch();

    const defaultImage = window?.vajofoMainMenuLocalizer?.defaultImage;

    const [quantity, setQuantity] = useState(1);
    const [fullPrice, setFullPrice] = useState(0);

    useEffect(() => {

        setQuantity(item.quantity || 1);
        setFullPrice(item.price_per_unit);

        if (item.selected_add_ons.length === 0) return;

        calculateAddOnsPrice()
    }, [])

    useEffect(() => {

        dispatch(updateCartItemQuantity({ 
            itemIndex: index,
            quantity: quantity 
        }));
    }, [quantity]);

    const calculateAddOnsPrice = () => {

        let currentAddOnsPrice = 0;

        item.selected_add_ons.forEach(addOn => {

            const addOnPrice = parseFloat(addOn.price) * parseInt(addOn.quantity);

            currentAddOnsPrice += addOnPrice;
        })

        setFullPrice(prevPrice => prevPrice + parseFloat(currentAddOnsPrice || 0))
    };

    const handleRemoveItem = () => {

        dispatch(deleteFromCart({ itemIndex: index }));
    };

    const globalSettings = useSelector(state => state.globalSettings.settings);

    const currencySymbol = getCurrencySymbol(globalSettings);

    return (
        <div className="cart-item">

            <div className="cart-item-details">

                <div className="item-info">

                    <img
                        src={item.thumbnail || defaultImage}
                        className="item-image"
                        alt={item.name || __('Menu Item', 'olena-food-ordering')}
                        onError={(e) => {
                            if (e.target.src !== defaultImage) {
                                e.target.src = defaultImage;
                            }
                        }}
                    />

                    <div className="item-details">
                        <div className="cart-item-name">
                            <NavLink to={`/item/${item.id}`}>
                                {item.name}
                            </NavLink>
                        </div>
                        <div className="item-customization">
                            <DishDescription
                                description={item.description}
                            />
                        </div>
                    </div>
                </div>
                <div className="price">{currencySymbol}{parseFloat(fullPrice).toFixed(2)}</div>
                <div className="quantity-controls">
                    <button
                        className="quantity-btn"
                        onClick={() => setQuantity(Math.max(1, quantity - 1))}
                        disabled={quantity === 1}
                    >−</button>
                    <span className="quantity-value">{quantity}</span>
                    <button
                        className="quantity-btn"
                        onClick={() => setQuantity(prevQ => prevQ < 10 ? prevQ + 1 : prevQ)}
                        disabled={quantity === 10}
                    >+</button>
                </div>
                <div className="subtotal">
                    {currencySymbol}{parseFloat(parseFloat(fullPrice || 0) * quantity).toFixed(2)}
                </div>
                <button 
                    className="remove-btn"
                    onClick={handleRemoveItem}
                >×</button>
            </div>

            {
                item.selected_add_ons.length>0 && 
                <div className="cart-item-add-ons">
                    <strong>Add-Ons:</strong>

                    {
                        item.selected_add_ons.map(item => (
                            <span key={`selected-add-on-${item.id}`}>{item.name} ({item.quantity})</span>
                        ))
                    }
                    
                </div>
            }

        </div>
    )
};