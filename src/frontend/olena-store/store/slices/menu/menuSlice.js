import { createSlice } from "@reduxjs/toolkit"

const initialState = {
    menuItems: [],
    currentPage: 1,
    perPage: 10,
}

const menuSlice = createSlice( {
    name: 'main-menu',
    initialState,
    reducers: {
        setMenuItems: ( state, action ) => {

            if( ! action.payload ) return
            
            const { data } = action.payload

            state.menuItems = data
        },
        setCurrentPage: ( state, action ) => {
            
            state.currentPage = action.payload
        },
        setPerPage: ( state, action ) => {
            state.perPage = action.payload
        },
        addMenuItem: (state, action) => {
            if (!action.payload?.item) return;

            const menuItems = state.menuItems?.menuItems || [];
            const newItem = action.payload.item;

            // Only add if item doesn't already exist
            if (!menuItems.find(item => item.id === newItem.id)) {
                state.menuItems = {
                    ...state.menuItems,
                    menuItems: [...menuItems, newItem]
                };
            }
        }
    },
} )

export const {
    setMenuItems,
    setCurrentPage,
    setPerPage,
    addMenuItem
} = menuSlice.actions

export default menuSlice.reducer