import { createSlice } from "@reduxjs/toolkit"

const initialState = {
    menuItem: {}
}

const itemSlice = createSlice( {
    name: 'menu-item',
    initialState,
    reducers: {
        addMenuItem: ( state, action ) => {

            if( ! action.payload ) return
            
            const { item } = action.payload
            
            state.menuItem = item;
        }
    },
} )

export const {
    addMenuItem
} = itemSlice.actions

export default itemSlice.reducer