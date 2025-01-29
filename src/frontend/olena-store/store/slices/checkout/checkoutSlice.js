import { createSlice } from '@reduxjs/toolkit';
import { updateLocalStorage } from '@olenaStore/helpers';

const initialState = {
  orderData: localStorage.getItem('olenaOrderData') ? JSON.parse(localStorage.getItem('olenaOrderData')) : {}
};

const checkoutSlice = createSlice({
  name: 'checkout',
  initialState,
  reducers: {
    setOrder: (state, action) => {
      if (!action.payload) return;

      const { items, customerData, deliveryData, paymentData, orderPlaced, orderId } = action.payload

      state.orderData.items = items
      state.orderData.customerData = customerData
      state.orderData.deliveryData = deliveryData
      state.orderData.paymentData = paymentData
      state.orderData.orderId = orderId;
      state.orderData.orderPlaced = orderPlaced;
     
      updateLocalStorage('olenaOrderData', state.orderData);
    },
    clearOrder: (state) => {
      state.orderData.items = [];
      state.orderData.customerData = {};
      state.orderData.deliveryData = {};
      state.orderData.paymentData = {};
      state.orderData.orderId = null;
      state.orderData.orderPlaced = {
        utc: null
      };

      updateLocalStorage('olenaOrderData', state.orderData);
    }
  }
});

export const { setOrder, clearOrder } = checkoutSlice.actions;

export default checkoutSlice.reducer;
