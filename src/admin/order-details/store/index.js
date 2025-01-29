import { configureStore } from "@reduxjs/toolkit"
import API from "@orderDetails/services/API"
import notifySliceReducer from "@orderDetails/store/slices/notify/notifySlice"
import globalSettingsSliceReducer from "@orderDetails/store/slices/settings/globalSettings"

const store = configureStore( {
    reducer: {
        [API.reducerPath]: API.reducer,
        notify: notifySliceReducer,
        globalSettings: globalSettingsSliceReducer,
    },

    middleware: (getDefaultMiddleware) =>
        getDefaultMiddleware().concat(API.middleware),
    
    devTools: false
} )

export default store