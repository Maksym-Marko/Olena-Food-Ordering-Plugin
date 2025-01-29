import React, { useState, useEffect } from 'react';
import { useSelector, useDispatch } from 'react-redux';
import { __ } from '@wordpress/i18n';
import { NavLink } from "react-router-dom";
import { clearCart } from '@olenaStore/store/slices/cart/cartSlice';
import { useSubmitOrderMutation } from "@olenaStore/services/Order"
import { ErrorMessage } from '@olenaStore/components/ErrorMessage';
import { updateCustomerData, updateDeliveryData } from '@olenaStore/store/slices/customerData/customerDataSlice';
import { FormInput } from '@olenaStore/components/checkout/FormInput';
import { DeliveryOption } from '@olenaStore/components/checkout/DeliveryOption';
import { PaymentMethod } from '@olenaStore/components/checkout/PaymentMethod';
import { OrderItem } from '@olenaStore/components/checkout/OrderItem';
import { useNavigate } from 'react-router-dom';
import { setOrder } from '@olenaStore/store/slices/checkout/checkoutSlice';
import { getCurrencySymbol, isFreeDeliveryEnabled, getFreeDeliveryMinAmount } from '@olenaStore/helpers';
import { emptySelection } from '@olenaStore/store/slices/menu-selection/menuSelectionSlice';

// Main Checkout Component
const Checkout = () => {
  const dispatch = useDispatch();
  const navigate = useNavigate();
  const [selectedDelivery, setSelectedDelivery] = useState('carryout');
  const [selectedPayment, setSelectedPayment] = useState('pickup');

  const deliveryData = useSelector(state => state.customerData.deliveryData);

  const [deliveryAddress, setDeliveryAddress] = useState({
    street: deliveryData.street || '',
    city: deliveryData.city || '',
    postalCode: deliveryData.postalCode || ''
  });
  const cartItems = useSelector(state => state.cart.cartItems);
  const [cartTotal, setCartTotal] = useState(0);

  const customerData = useSelector(state => state.customerData.customerData);

  const [customerDetails, setCustomerDetails] = useState({
    firstName: customerData.firstName || '',
    lastName: customerData.lastName || '',
    email: customerData.email || '',
    phone: customerData.phone || ''
  });

  const [isSubmitting, setIsSubmitting] = useState(false);
  const [error, setError] = useState(null);

  const [fieldErrors, setFieldErrors] = useState({
    firstName: false,
    lastName: false,
    email: false,
    phone: false,
    street: false,
    city: false,
    postalCode: false
  });

  // Define delivery fees as constants
  const DELIVERY_FEES = window.olenaFoodOrdering?.deliveryMethods?.reduce((acc, method) => {
    acc[method.id] = method.fee;
    return acc;
  }, {}) || {
    'carryout': 0,
    'free-delivery': 0
  };

  // Get delivery fee based on selection and cart status
  const DELIVERY_FEE = cartItems.length > 0 ? DELIVERY_FEES[selectedDelivery] : 0;

  // Move the mutation hook to the top level
  const [submitOrder, { isLoading }] = useSubmitOrderMutation();

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

  useEffect(() => {
    
    if (error) {
      validateForm();
    }
  }, [customerDetails, deliveryAddress, selectedDelivery]);

  const handleAddressChange = (field, value) => {
    setDeliveryAddress(prev => ({
      ...prev,
      [field]: value
    }));
    
    // Clear error for this field if it has a value
    if (value) {
      setFieldErrors(prev => ({
        ...prev,
        [field]: false
      }));
    }
  };

  const handleCustomerDetailsChange = (field, value) => {
    // Update local state
    setCustomerDetails(prev => ({
      ...prev,
      [field]: value
    }));
        
    // Clear error for this field if it has a value
    if (value) {
      setFieldErrors(prev => ({
        ...prev,
        [field]: false
      }));
    }
  };

  const validateForm = () => {
    const errors = {
      firstName: !customerDetails.firstName,
      lastName: !customerDetails.lastName,
      email: !customerDetails.email,
      phone: !customerDetails.phone,
      street: selectedDelivery !== 'carryout' && !deliveryAddress.street,
      city: selectedDelivery !== 'carryout' && !deliveryAddress.city,
      postalCode: selectedDelivery !== 'carryout' && !deliveryAddress.postalCode
    };

    setFieldErrors(errors);
    
    const hasErrors = Object.values(errors).some(error => error);
    
    // Only set error message if there are errors, otherwise clear it
    if (hasErrors) {
      setError('Please fill in all required fields');
    } else {
      setError(null);
    }

    return !hasErrors;
  };

  const handlePlaceOrder = async () => {
    setError(null);
    
    // Validate form
    if (!validateForm()) {
      return;
    }

    // Update Redux store with customer data
    dispatch(updateCustomerData({
      customer: {
        ...customerDetails
      }
    }));

    dispatch(updateDeliveryData({
      delivery: {
        ...deliveryAddress
      }
    }));

    // Prepare order data
    const orderData = {
      customerData: customerDetails,
      deliveryData: {
        method: selectedDelivery,
        address: selectedDelivery !== 'carryout' ? deliveryAddress : null,
        fee: DELIVERY_FEE
      },
      paymentData: {
        method: selectedPayment
      },
      items: cartItems,
      totals: {
        subtotal: cartTotal,
        delivery: DELIVERY_FEE,
        total: cartTotal + DELIVERY_FEE
      }
    };

    try {
      const response = await submitOrder({orderData});

      if (response?.data?.status === 'success') {

        dispatch(clearCart());

        dispatch(setOrder({
          ...response?.data?.orderData,
          orderId: response?.data?.orderId
        }));

        // clear selection
        dispatch(emptySelection());

        // Redirect to receipt page
        navigate('/receipt');
      }
    } catch (err) {
      setError(err.message || 'Failed to submit order');
    }
  };

  const globalSettings = useSelector(state => state.globalSettings.settings);

  const currencySymbol = getCurrencySymbol(globalSettings);

  const freeDeliveryEnabled = isFreeDeliveryEnabled(globalSettings);

  const freeDeliveryMinAmount = getFreeDeliveryMinAmount(globalSettings);  

  return (
    <div className="checkout-container">
      {/* Left Column - Checkout Forms */}
      <div className="checkout-main">
        {/* Customer Details */}
        <div className="checkout-section">

          <div className="delivery-details-title">Customer Details</div>
          
          <div className="form-grid">
            <FormInput 
              label="First Name" 
              type="text" 
              required 
              value={customerDetails.firstName}
              onChange={(e) => handleCustomerDetailsChange('firstName', e.target.value)}
              hasError={fieldErrors.firstName}
            />
            <FormInput 
              label="Last Name" 
              type="text" 
              required 
              value={customerDetails.lastName}
              onChange={(e) => handleCustomerDetailsChange('lastName', e.target.value)}
              hasError={fieldErrors.lastName}
            />
            <FormInput 
              label="Email" 
              type="email" 
              required 
              value={customerDetails.email}
              onChange={(e) => handleCustomerDetailsChange('email', e.target.value)}
              hasError={fieldErrors.email}
            />
            <FormInput 
              label="Phone" 
              type="tel" 
              required 
              value={customerDetails.phone}
              onChange={(e) => handleCustomerDetailsChange('phone', e.target.value)}
              hasError={fieldErrors.phone}
            />
          </div>
        </div>

        {/* Delivery Details */}
        <div className="checkout-section">
          <div className="delivery-details-title">Delivery Details</div>

          <div>
            <h3 className="section-title">Delivery Options</h3>
            <div className="delivery-options">

              {window.olenaFoodOrdering?.deliveryMethods?.length > 0 ? (
                window.olenaFoodOrdering.deliveryMethods.map(method => (
                  <DeliveryOption
                    key={method.id}
                    title={method.title}
                    description={method.description}
                    price={currencySymbol + method.fee.toFixed(2)}
                    isSelected={selectedDelivery === method.id}
                    onChange={() => setSelectedDelivery(method.id)}
                  />
                ))
              ) : (
                <>
                  <DeliveryOption
                    title="Carryout"
                    description="Pick up your order at our restaurant"
                    price="0.00"
                    isSelected={selectedDelivery === 'carryout'}
                    onChange={() => setSelectedDelivery('carryout')}
                  />

                  {freeDeliveryEnabled && (
                    <DeliveryOption
                      title="Free Delivery"
                      description={globalSettings?.free_delivery_requirements?.value}
                      price="0.00"
                      isSelected={selectedDelivery === 'free-delivery'}
                      onChange={() => setSelectedDelivery('free-delivery')}
                      disabled={cartTotal < freeDeliveryMinAmount}
                    />
                  )}
                </>
              )}
            </div>
          </div>

          {/* Only show address form if delivery is selected */}
          {selectedDelivery !== 'carryout' && (
            <div>
              <h3 className="section-title">Delivery Address</h3>
              <div className="form-grid">
                <FormInput
                  label="Street Address"
                  type="text"
                  required
                  fullWidth
                  value={deliveryAddress.street}
                  onChange={(e) => handleAddressChange('street', e.target.value)}
                  hasError={fieldErrors.street}
                />
                <FormInput
                  label="City"
                  type="text"
                  required
                  value={deliveryAddress.city}
                  onChange={(e) => handleAddressChange('city', e.target.value)}
                  hasError={fieldErrors.city}
                />
                <FormInput
                  label="Postal Code"
                  type="text"
                  required
                  value={deliveryAddress.postalCode}
                  onChange={(e) => handleAddressChange('postalCode', e.target.value)}
                  hasError={fieldErrors.postalCode}
                />
              </div>
            </div>
          )}
        </div>

        {/* Payment Method */}
        <div className="checkout-section">
          
          <div className="delivery-details-title">{__('Payment Methods', 'olena-food-ordering')}</div>

          <div className="payment-methods">            

            {/* Check for custom payment methods and render them if they exist */}
            {window.olenaFoodOrdering?.paymentMethods?.length > 0 ? (
              window.olenaFoodOrdering.paymentMethods.map(method => (
                <PaymentMethod
                  key={method.id}
                  title={method.title}
                  description={method.description}
                  isSelected={selectedPayment === method.id}
                  onChange={() => setSelectedPayment(method.id)}
                />
              ))
            ) : (
              <PaymentMethod
                title="Pay at Pickup"
                description="Pay when you pick up your order"
                isSelected={selectedPayment === 'pickup'}
                onChange={() => setSelectedPayment('pickup')}
              />
            )}
          </div>
        </div>
      </div>

      {/* Right Column - Order Summary */}
      <div className="order-summary">
        <h2 className="section-title">Order Summary</h2>

        <div className="order-items">
          {cartItems.map((item, index) => (
            <OrderItem
              key={index}
              quantity={item.quantity}
              name={item.name}
              price={(item.price_per_unit * item.quantity).toFixed(2)}
              selected_add_ons={item.selected_add_ons}
            />
          ))}
        </div>

        <div className="summary-divider"></div>

        <div className="total-row">
          <span>{__('Subtotal', 'olena-food-ordering')}</span>
          <span>{currencySymbol}{cartTotal.toFixed(2)}</span>
        </div>
        <div className="total-row">
          <span>{__('Delivery Fee', 'olena-food-ordering')}</span>
          <span>{currencySymbol}{DELIVERY_FEE.toFixed(2)}</span>
        </div>

        <div className="total-amount">
          <span>{__('Total', 'olena-food-ordering')}</span>
          <span>{currencySymbol}{(cartTotal + DELIVERY_FEE).toFixed(2)}</span>
        </div>

        {error && <ErrorMessage>{error}</ErrorMessage>}

        {cartItems.length > 0 && (
          <button 
            className="place-order-btn"
            onClick={handlePlaceOrder}
          >
            {isSubmitting && !error ? __('Processing...', 'olena-food-ordering') : __('Place Order', 'olena-food-ordering')}
          </button>
        )}

        <div className="navigation-links">
          <NavLink
            to='/cart'
            className="back-to-cart"
          >
            {__('Back to Cart', 'olena-food-ordering')}
          </NavLink>

          <NavLink
            to='/'
            className="continue-shopping"
          >
            {__('Continue Shopping', 'olena-food-ordering')}
          </NavLink>
        </div>
      </div>
    </div>
  );
};

export default Checkout;
