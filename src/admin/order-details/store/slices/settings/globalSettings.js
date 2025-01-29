import { createSlice } from "@reduxjs/toolkit"

const initialState = {
    settings: [],
}

const globalSettingsSlice = createSlice({
    name: 'globalSettings',
    initialState,
    reducers: {
        setGlobalSettings: (state, action) => {

            const { settings } = action.payload

            if(!settings) return

            state.settings = settings
        },
    }
})

export const { 
    setGlobalSettings,
} = globalSettingsSlice.actions

export default globalSettingsSlice.reducer