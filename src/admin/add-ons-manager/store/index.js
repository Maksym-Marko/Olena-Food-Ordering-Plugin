import { configureStore } from "@reduxjs/toolkit"
import API from "@addOnsManager/services/API"
import addOnsManagerSliceReducer from "@addOnsManager/store/slices/addOns/addOnsManagerSlice"
import notifySliceReducer from "@addOnsManager/store/slices/notify/notifySlice"
import globalSettingsSliceReducer from "@addOnsManager/store/slices/settings/globalSettings"

const store = configureStore( {
    reducer: {
        [API.reducerPath]: API.reducer,
        addOnsManager: addOnsManagerSliceReducer,
        notify: notifySliceReducer,
        globalSettings: globalSettingsSliceReducer,
    },

    middleware: (getDefaultMiddleware) =>
        getDefaultMiddleware().concat(API.middleware),
    
    devTools: false
} )

export default store