import { createSlice } from "@reduxjs/toolkit"
import { updateLocalStorage } from '@olenaStore/helpers';

const initialState = {
    customerData: localStorage.getItem('olenaCustomerData') ? JSON.parse(localStorage.getItem('olenaCustomerData')) : {},
    
    /*
    {
        firstName: 'John',
        lastName: 'Doe',
        email: 'john.doe@example.com',
        phone: '1234567890'
    }
    */

    deliveryData: localStorage.getItem('deliveryData') ? JSON.parse(localStorage.getItem('deliveryData')) : {}
    /*
    {
        street: '1234 Main St, Anytown, USA',
        city: 'Anytown',
        postalCode: '12345'
    }
    */
}

const customerDataSlice = createSlice( {
    name: 'main-menu',
    initialState,
    reducers: {
        updateCustomerData: (state, action) => {

            if( ! action.payload ) return
            
            const { customer } = action.payload

            state.customerData = customer;

            updateLocalStorage('olenaCustomerData', state.customerData);
        },
        updateDeliveryData: (state, action) => {
            if( ! action.payload ) return
            
            const { delivery } = action.payload

            state.deliveryData = delivery;

            updateLocalStorage('deliveryData', state.deliveryData);
        },
    },
} )

export const {
    updateCustomerData,
    updateDeliveryData
} = customerDataSlice.actions

export default customerDataSlice.reducer