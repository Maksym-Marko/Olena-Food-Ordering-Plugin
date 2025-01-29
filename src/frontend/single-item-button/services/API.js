import { createApi, fetchBaseQuery } from '@reduxjs/toolkit/query/react'

const baseQuery = fetchBaseQuery({

    baseUrl: `${window.location.origin}/wp-json/olena-food-ordering/v1`,
    credentials: 'same-origin',
    prepareHeaders: (headers, { getState }) => {
        headers.set('Content-Type', 'application/json');
        headers.set('Accept', 'application/json');

        // Add WordPress REST API nonce
        headers.set('X-WP-Nonce', vajofoSingleItemButtonLocalizer.nonce);

        return headers;
    }
})

const handleResponse = async (args, api, extraOptions) => {

    let result = await baseQuery(args, api, extraOptions)

    return result
}

const API = createApi({
    baseQuery: handleResponse,
    endpoints: builder => ({}),
})

export default API