import { __, _n, sprintf } from '@wordpress/i18n'
import { useDispatch, useSelector } from "react-redux"
import { NavLink } from "react-router-dom"
import { CartItem } from '@olenaStore/components/CartItem'
import { useEffect, useState } from 'react';
import { getCurrencySymbol } from '@olenaStore/helpers';

const Cart = () => {

    const cartItems = useSelector(state => state.cart.cartItems);
    const [cartTotal, setCartTotal] = useState(0);
    const DELIVERY_FEE = cartItems.length > 0 ? 5.00 : 0;

    useEffect(() => {
        // Calculate cart totals whenever cartItems changes
        const newTotal = cartItems.reduce((total, item) => {
            // Calculate item subtotal (price × quantity)
            const itemSubtotal = item.price_per_unit * item.quantity;
            
            // Calculate add-ons subtotal
            const addOnsSubtotal = item.selected_add_ons.reduce((addOnTotal, addOn) => {
                return addOnTotal + (addOn.price * addOn.quantity);
            }, 0);

            // Return running total + current item total (including add-ons)
            return total + (itemSubtotal + (addOnsSubtotal * item.quantity));
        }, 0);

        setCartTotal(newTotal);
    }, [cartItems]);

    const globalSettings = useSelector(state => state.globalSettings.settings);

    const currencySymbol = getCurrencySymbol(globalSettings);

    return (

        <div className="cart-container">

            <div className="cart-items">
                <h2 className="page-title">
                {sprintf(
                    _n(
                        'Your Cart (%d item)',
                        'Your Cart (%d items)',
                        cartItems.length,
                        'olena-food-ordering'
                    ),
                    cartItems.length
                )}
                    
                </h2>

                <div className="cart-header">
                    <div>Item</div>
                    <div>Price</div>
                    <div>Quantity</div>
                    <div>Subtotal</div>
                    <div></div>
                </div>

                {cartItems.length > 0 ? (
                    cartItems.map((item, index) => (
                        <CartItem 
                            key={`${item.id}-${index}`}
                            item={item} 
                            index={index}
                        />
                    ))
                ) : (
                    <div className="empty-cart-message">
                        <p>{__('Your cart is empty', 'olena-food-ordering')}</p>
                        <NavLink to="/" className="continue-shopping">
                            {__('Continue Shopping', 'olena-food-ordering')}
                        </NavLink>
                    </div>
                )}

            </div>

            {/* <!-- Cart Summary Section --> */}
            <div className="cart-summary">
                <h2 className="summary-title">Order Summary</h2>

                <div className="summary-row">
                    <span>Subtotal</span>
                    <span>{currencySymbol}{cartTotal.toFixed(2)}</span>
                </div>

                {/* <div className="summary-row">
                    <span>Tax</span>
                    <span>$6.31</span>
                </div>

                <div className="summary-row">
                    <span>Applied Discount</span>
                    <span>-$7.89</span>
                </div> */}

                <div className="summary-total">
                    <span>Total</span>
                    <span>{currencySymbol}{(cartTotal).toFixed(2)}</span>
                </div>

                <NavLink to="/checkout" className="checkout-btn">
                    {__('Proceed to Checkout', 'olena-food-ordering')}
                </NavLink>

                <NavLink
                    to='/'
                    className="continue-shopping"
                >
                    {__('Continue Shopping', 'olena-food-ordering')}
                </NavLink>

            </div>
        </div>
    )
};

export default Cart;