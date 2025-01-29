import { configureStore } from "@reduxjs/toolkit"
import API from "@settings/services/API"
import settingsSliceReducer from "@settings/store/slices/settings/settingsSlice"
import notifySliceReducer from "@settings/store/slices/notify/notifySlice"

const store = configureStore( {
    reducer: {
        [API.reducerPath]: API.reducer,
        settings: settingsSliceReducer,
        notify: notifySliceReducer,
    },

    middleware: (getDefaultMiddleware) =>
        getDefaultMiddleware().concat(API.middleware),
    
    devTools: false
} )

export default store