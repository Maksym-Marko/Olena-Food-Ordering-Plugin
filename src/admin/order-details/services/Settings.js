import API from "@orderDetails/services/API"

const Settings = API.injectEndpoints({

    endpoints: builder => ({
        getGlobalSettings: builder.query({

            query: () => '/get-settings',
        }),
    })
})

export default Settings

export const {
    useGetGlobalSettingsQuery,
} = Settings