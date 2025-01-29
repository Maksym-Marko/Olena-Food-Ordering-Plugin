import API from "@settings/services/API"

const Settings = API.injectEndpoints( {

    endpoints: builder => ( {
        setSettings: builder.mutation( {
            query: settings => ( {
                url: '/update-settings',
                method: 'POST',
                body: { ...settings }
            } )
        } ),        
        getSettings: builder.query( {
            query: () => '/get-settings', 
        } ),
    } )
} )

export default Settings

export const {
    useSetSettingsMutation,
    useGetSettingsQuery
 } = Settings