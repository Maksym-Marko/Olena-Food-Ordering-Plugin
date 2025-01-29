import { createSlice } from "@reduxjs/toolkit"
import { updateLocalStorage } from '@olenaStore/helpers';

const initialState = {
    cartItems: localStorage.getItem('olenaCartItems') ? JSON.parse(localStorage.getItem('olenaCartItems')) : []
}

const cartSlice = createSlice( {
    name: 'main-menu',
    initialState,
    reducers: {
        addToCart: ( state, action ) => {

            if( ! action.payload ) return
            
            const { item } = action.payload

            const itemCopy = {
                ...item,
                timestamps: {
                    ...item.timestamps,
                    addedToCart: {
                        ...(item.timestamps?.addedToCart || {}),
                        utc: new Date().toISOString()
                    }
                }
            };

            state.cartItems = [...state.cartItems, itemCopy];

            updateLocalStorage('olenaCartItems', state.cartItems);
        },
        deleteFromCart: (state, action) => {
            if (!action.payload) return
            
            const { itemIndex } = action.payload

            // Validate index is within bounds
            if (itemIndex >= 0 && itemIndex < state.cartItems.length) {
                state.cartItems = state.cartItems.filter((_, index) => index !== itemIndex);
            }

            updateLocalStorage('olenaCartItems', state.cartItems);

        },
        updateCartItemQuantity: (state, action) => {
            const { itemIndex, quantity } = action.payload;
            
            // Validate index is within bounds
            if (itemIndex >= 0 && itemIndex < state.cartItems.length) {
                state.cartItems[itemIndex].quantity = quantity;
                
                // Update lastModified timestamp
                state.cartItems[itemIndex].timestamps = {
                    ...state.cartItems[itemIndex].timestamps,
                    lastModified: {
                        utc: new Date().toISOString()
                    }
                };
            }

            updateLocalStorage('olenaCartItems', state.cartItems);
        },
        clearCart: (state) => {
            state.cartItems = [];

            updateLocalStorage('olenaCartItems', state.cartItems);
        },
    },
} )

export const {
    addToCart,
    deleteFromCart,
    updateCartItemQuantity,
    clearCart
} = cartSlice.actions

export default cartSlice.reducer