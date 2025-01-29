import { createSlice } from "@reduxjs/toolkit"

const initialState = {
    success: [],
    warnings: [],
    errors: [],
}

const notifySlice = createSlice({
    name: 'notify',
    initialState,
    reducers: {
        setSuccess: (state, action) => {

            const { message } = action.payload

            if(!message) return

            state.success.push(message)
        },
        clearSuccess: (state, action) => {

            if(action?.payload?.type && typeof action?.payload?.index === 'number') {

                const {type, index} = action.payload

                state[type].splice(index, 1);
            } else {

                state.success = []
            }
        },

        setWarning: (state, action) => {

            const { message } = action.payload

            if(!message) return

            state.warnings.push(message)
        },
        clearWarnings: (state, action) => {

            if(action?.payload?.type && typeof action?.payload?.index === 'number') {

                const {type, index} = action.payload

                state[type].splice(index, 1);
            } else {

                state.warnings = []
            }
        },

        setErrors: (state, action) => {

            const { message } = action.payload

            if(!message) return

            state.errors.push(message)
        },
        clearErrors: (state, action) => {

            if(action?.payload?.type && typeof action?.payload?.index === 'number') {

                const {type, index} = action.payload

                state[type].splice(index, 1);
            } else {

                state.errors = []
            }
        }
    }
})

export const { 
    setSuccess,
    clearSuccess,
    setWarning,
    clearWarnings,
    setErrors,
    clearErrors
} = notifySlice.actions

export default notifySlice.reducer