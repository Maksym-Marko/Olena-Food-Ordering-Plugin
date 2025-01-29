import API from "@settings/services/API"

const DemoImport = API.injectEndpoints( {

    endpoints: builder => ( {
        demoImport: builder.mutation( {
            query: step => ( {
                url: '/demo-import',
                method: 'POST',
                body: { ...step }
            } )
        } ),
        getImportDataInfo: builder.query( {
            query: () => '/get-demo-import-info', 
        } ),
    } )
} )

export default DemoImport

export const {
    useDemoImportMutation,
    useGetImportDataInfoQuery
 } = DemoImport