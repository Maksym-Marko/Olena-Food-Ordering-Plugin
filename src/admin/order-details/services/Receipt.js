import API from "@orderDetails/services/API"

const Receipt = API.injectEndpoints( {

    endpoints: builder => ( {     
        getReceipt: builder.query( {
            query: (params = {}) => ({
                url: '/get-receipt',
                params: {
                    orderId: params.orderId || window?.wpApiOrderDetails?.orderId || 0,
                },
            }),
        } ),
    } )
} )

export default Receipt

export const {
    useGetReceiptQuery
 } = Receipt