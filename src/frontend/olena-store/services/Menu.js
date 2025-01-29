import API from "@olenaStore/services/API"

const Menu = API.injectEndpoints({

    endpoints: builder => ({
        getMenuItems: builder.query({
            query: (params = {}) => ({
                url: '/get-menu-items',
                params: {
                    currentPage: params.currentPage || 1,
                    perPage: params.perPage || 10,
                    order: params.order || 'ASC'
                },
            }),
        }),
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

export default Menu

export const {
    useGetMenuItemsQuery,
    useGetMenuItemQuery
} = Menu