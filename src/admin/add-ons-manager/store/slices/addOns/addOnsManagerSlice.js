import { createSlice } from "@reduxjs/toolkit"

const initialState = {
    selectedAddons: {},
    availableAddons: {},
}

const addOnsManagerSlice = createSlice({
    name: 'addOnsManager',
    initialState,
    reducers: {
        setSelectedAddOns: (state, action) => {

            if (!action.payload) return

            const { data } = action.payload

            state.selectedAddons = {
                ...state.selectedAddons,
                ...data
            }
        },
        setAvailableAddons: (state, action) => {

            if (!action.payload) return

            const { data } = action.payload

            state.availableAddons = data
        },
        setMinNumber: (state, action) => {

            if (!action.payload) return

            const { categoryId, addOnId, newMin } = action.payload

            if (!categoryId || !addOnId || newMin === undefined) return

            if (!state.selectedAddons?.[categoryId]?.[addOnId]) return

            state.selectedAddons[categoryId][addOnId].min = parseInt(newMin)
        },
        setMaxNumber: (state, action) => {

            if (!action.payload) return

            const { categoryId, addOnId, newMax } = action.payload

            if (!categoryId || !addOnId || newMax === undefined) return

            if (!state.selectedAddons?.[categoryId]?.[addOnId]) return

            state.selectedAddons[categoryId][addOnId].max = parseInt(newMax)
        },
        setSelectedAddOn: (state, action) => {

            if (!action.payload) return

            const { categoryId, addOnId } = action.payload

            if (!state.selectedAddons[categoryId]) {

                state.selectedAddons[categoryId] = {};
            }

            state.selectedAddons[categoryId][addOnId] = {
                min: 0,
                max: 1
            }
        },
        removeSelectedAddon: (state, action) => {

            if (!action.payload) return

            const { categoryId, addOnId } = action.payload

            if (!state.selectedAddons?.[categoryId]) return;

            const { [addOnId]: removed, ...rest } = state.selectedAddons[categoryId];

            if (Object.keys(rest).length === 0) {

                delete state.selectedAddons[categoryId];
            } else {

                state.selectedAddons[categoryId] = rest;
            }
        },
        removeSelectedCategory: (state, action) => {

            if (!action.payload) return

            const { categoryId } = action.payload

            if (!state.selectedAddons?.[categoryId]) return;

            delete state.selectedAddons[categoryId];
        },

        /* Update available add-on category */
        updateAvailableAddOnsCategory: (state, action) => {

            if (!action.payload) return

            const { categoryId, name, slug } = action.payload

            if (!state.availableAddons?.[categoryId]) return;

            if (!name || !slug) return;

            // Get the category object
            const category = state.availableAddons[categoryId];

            // Update the category properties
            category.name = name;
            category.slug = slug;

            // Or if you need to create a new object:
            state.availableAddons[categoryId] = {
                ...state.availableAddons[categoryId],
                name,
                slug
            };
        },

        /* Update available add-on */
        updateAvailableAddOn: (state, action) => {

            if (!action.payload) return

            const { categoryId, addOnId, name, price } = action.payload

            if (!state.availableAddons?.[categoryId]) return;

            if (!state.availableAddons[categoryId].add_ons
                ?.[addOnId]) return;

            if (!name || !price) return;

            state.availableAddons[categoryId].add_ons[addOnId] = {
                ...state.availableAddons[categoryId].add_ons[addOnId],
                name,
                price
            };
        },

        /* Create available add-on */
        addAvailableAddOn: (state, action) => {

            if (!action.payload) return

            const { categoryId, addOnId, name, price } = action.payload

            if (!state.availableAddons?.[categoryId]) {

                state.availableAddons[categoryId] = {}
            }

            if (!state.availableAddons[categoryId].add_ons?.addOnId) {

                state.availableAddons[categoryId].add_ons[addOnId] = {
                    name,
                    price
                }
            }
        },

        /* Delete available add-on */
        deleteAvailableAddOn: (state, action) => {

            if (!action.payload) return;

            const { categoryId, addOnId } = action.payload;

            if (!state.availableAddons?.[categoryId]) return;

            if (!state.availableAddons[categoryId].add_ons?.[addOnId]) return;

            delete state.availableAddons[categoryId].add_ons[addOnId];
        },

        /* Add available add-ons category */
        addAvailableAddOnCategory: (state, action) => {

            if (!action.payload) return;

            const { categoryId, name, slug } = action.payload;

            if (state.availableAddons?.[categoryId]) return;

            state.availableAddons[categoryId] = {
                name,
                slug,
                add_ons: {}
            }
        },

        /* Delete available add-ons category */
        deleteAvailableAddOnsCategory: (state, action) => {

            if (!action.payload) return;

            const { categoryId } = action.payload;

            if (!state.availableAddons?.[categoryId]) return;

            delete state.availableAddons[categoryId]
        }
    }
})

export const {
    setSelectedAddOns,
    setAvailableAddons,
    setMinNumber,
    setMaxNumber,
    setSelectedAddOn,
    removeSelectedAddon,
    removeSelectedCategory,
    updateAvailableAddOnsCategory,
    updateAvailableAddOn,
    addAvailableAddOn,
    deleteAvailableAddOn,
    addAvailableAddOnCategory,
    deleteAvailableAddOnsCategory
} = addOnsManagerSlice.actions

export default addOnsManagerSlice.reducer