import { configureStore } from "@reduxjs/toolkit"
import API from "@singleItemButton/services/API"
import itemSliceReducer from "@singleItemButton/store/slices/itemSlice"

const store = configureStore( {
    reducer: {
        [API.reducerPath]: API.reducer,
        item: itemSliceReducer
    },

    middleware: (getDefaultMiddleware) =>
        getDefaultMiddleware().concat(API.middleware),
    
    devTools: false
} )

export default store