import { createSlice } from "@reduxjs/toolkit"
import { extractMenuItemData, updateLocalStorage } from '@olenaStore/helpers'

const initialState = {
    menuItems: localStorage.getItem('olenaSelectedMenuItems') ? JSON.parse(localStorage.getItem('olenaSelectedMenuItems')) : []
}

const menuSelectionSlice = createSlice({
    name: 'menu-selection',
    initialState,
    reducers: {
        selectMenuItem: (state, action) => {

            if (!action.payload) return

            const { item } = action.payload

            const existingItemIndex = state.menuItems.findIndex(

                selection => selection.id === item.id
            );

            if (existingItemIndex === -1) {

                state.menuItems.push(extractMenuItemData(item));
            } else {

                state.menuItems[existingItemIndex].timestamps.lastModified.utc = new Date().toISOString()
            }

            updateLocalStorage('olenaSelectedMenuItems', state.menuItems);
        },
        modifyAddOn: (state, action) => {

            if (!action.payload) return

            const { addon, menuItem } = action.payload

            const existingItemIndex = state.menuItems.findIndex(

                selection => selection.id === menuItem.id
            );

            if (existingItemIndex === -1) {

                state.menuItems.push(extractMenuItemData(menuItem));
            }

            const existingAddOnIndex = state.menuItems[existingItemIndex].selected_add_ons.findIndex(

                item => item.id === addon.id
            );

            if(existingAddOnIndex === -1) {

                state.menuItems[existingItemIndex].selected_add_ons.push(addon);
            } else {

                state.menuItems[existingItemIndex].selected_add_ons[existingAddOnIndex] = addon
            }

            updateLocalStorage('olenaSelectedMenuItems', state.menuItems);
        },
        deleteAddOn: (state, action) => {

            if (!action.payload) return

            const { addon, menuItem } = action.payload

            const existingItemIndex = state.menuItems.findIndex(

                selection => selection.id === menuItem.id
            );

            if (existingItemIndex === -1) return;

            state.menuItems[existingItemIndex].selected_add_ons = 
                state.menuItems[existingItemIndex].selected_add_ons.filter(item => item.id !== addon.id);

            updateLocalStorage('olenaSelectedMenuItems', state.menuItems);
        },
        emptyAddOns: (state, action) => {

            if (!action.payload) return

            const { itemId } = action.payload

            const existingItemIndex = state.menuItems.findIndex(

                selection => selection.id === itemId
            );

            if (existingItemIndex === -1) return;

            state.menuItems[existingItemIndex] = {
                ...state.menuItems[existingItemIndex],
                selected_add_ons: []
            };

            updateLocalStorage('olenaSelectedMenuItems', state.menuItems);
        },
        emptySelection: (state) => {

            state.menuItems = [];

            updateLocalStorage('olenaSelectedMenuItems', state.menuItems);
        }
    }
})

export const {
    selectMenuItem,
    modifyAddOn,
    deleteAddOn,
    emptyAddOns,
    emptySelection
} = menuSelectionSlice.actions

export default menuSelectionSlice.reducer