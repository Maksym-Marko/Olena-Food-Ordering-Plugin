import API from "@addOnsManager/services/API"

const AddOns = API.injectEndpoints({

    endpoints: builder => ({

        setSelectedAddOns: builder.mutation({

            query: ({ selectedAddons, postId }) => ({
                url: '/set-selected-add-ons',
                method: 'POST',
                body: {
                    selectedAddons,
                    postId
                }
            })
        }),
        getSelectedAddOns: builder.query({

            query: (postId) => `/get-selected-add-ons/${postId}`,
        }),
        getAvailableAddOns: builder.query({

            query: () => '/get-available-add-ons',
        }),

        /* update category name and slug */
        updateAddOnsCategory: builder.mutation({

            query: ({ categoryId, newName, newSlug }) => ({
                url: '/update-add-ons-category',
                method: 'POST',
                body: {
                    categoryId,
                    newName,
                    newSlug
                }
            })
        }),

        /* update add-on name and price */
        updateAddOn: builder.mutation({

            query: ({ categoryId, addOnId, newName, newPrice }) => ({
                url: '/update-add-on',
                method: 'POST',
                body: {
                    categoryId,
                    addOnId,
                    newName,
                    newPrice
                }
            })
        }),

        /* create add-on */
        createAddOn: builder.mutation({

            query: ({ categoryId, name, slug, price, description }) => ({
                url: '/create-add-on',
                method: 'POST',
                body: {
                    categoryId,
                    name,
                    slug,
                    price,
                    description
                }
            })
        }),

        /* delete add-on */
        deleteAddOn: builder.mutation({

            query: ({ addOnId }) => ({
                url: '/delete-add-on',
                method: 'POST',
                body: {
                    addOnId
                }
            })
        }),

        /* create add-on category */
        createCategory: builder.mutation({

            query: ({ name, slug, description }) => ({
                url: '/create-add-on-category',
                method: 'POST',
                body: {
                    name,
                    slug,
                    description
                }
            })
        }),

        /* Delete add-on category */
        deleteAddOnsCategory: builder.mutation({

            query: ({ categoryId }) => ({
                url: '/delete-add-on-category',
                method: 'POST',
                body: {
                    categoryId
                }
            })
        }),

    })
})

export default AddOns

export const {
    useSetSelectedAddOnsMutation,
    useGetSelectedAddOnsQuery,
    useGetAvailableAddOnsQuery,
    useUpdateAddOnsCategoryMutation,
    useUpdateAddOnMutation,
    useCreateAddOnMutation,
    useDeleteAddOnMutation,
    useCreateCategoryMutation,
    useDeleteAddOnsCategoryMutation
} = AddOns