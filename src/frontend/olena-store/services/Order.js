import API from "@olenaStore/services/API"

const Order = API.injectEndpoints({
    endpoints: builder => ({

        submitOrder: builder.mutation({

            query: ({ orderData }) => ({
                url: '/submit-order',
                method: 'POST',
                body: { orderData }
            })
        }),

    })
});


export default Order

export const {
    useSubmitOrderMutation
} = Order