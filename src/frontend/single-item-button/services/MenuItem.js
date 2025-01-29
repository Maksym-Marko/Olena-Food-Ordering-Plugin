import API from "@singleItemButton/services/API"

const MenuItem = API.injectEndpoints({

    endpoints: builder => ({
        getMenuItem: builder.query({
            query: (params = {}) => ({
                url: '/get-menu-item',
                params: {
                    postId: params.postId
                },
            }),
        }),
    })
});

export default MenuItem

export const {
    useGetMenuItemQuery
} = MenuItem