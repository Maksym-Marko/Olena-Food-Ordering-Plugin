import { configureStore } from "@reduxjs/toolkit"
import API from "@olenaStore/services/API"
import menuSliceReducer from "@olenaStore/store/slices/menu/menuSlice"
import menuSelectionSliceReducer from "@olenaStore/store/slices/menu-selection/menuSelectionSlice"
import notifySliceReducer from "@olenaStore/store/slices/notify/notifySlice"
import cartSliceReducer from "@olenaStore/store/slices/cart/cartSlice"
import customerDataSliceReducer from "@olenaStore/store/slices/customerData/customerDataSlice"
import checkoutSliceReducer from "@olenaStore/store/slices/checkout/checkoutSlice"
import globalSettingsSliceReducer from "@olenaStore/store/slices/settings/globalSettings"

const eventMiddleware = store => next => action => {
    const result = next(action);
    const customEvent = new CustomEvent('olenaStoreChangedEvent', {
        detail: {
            type: 'OLENA_STORE_CHANGED',
            // timestamp: new Date().getTime()
        },
        bubbles: true
    });
    document.dispatchEvent(customEvent);
    return result;
};

const store = configureStore({
    reducer: {
        [API.reducerPath]: API.reducer,
        notify: notifySliceReducer,
        menu: menuSliceReducer,
        menuSelection: menuSelectionSliceReducer,
        cart: cartSliceReducer,
        checkout: checkoutSliceReducer,
        customerData: customerDataSliceReducer,
        globalSettings: globalSettingsSliceReducer
    },

    middleware: (getDefaultMiddleware) =>
        getDefaultMiddleware()
            .concat(API.middleware)
            .concat(eventMiddleware),

    devTools: false
})

export default store