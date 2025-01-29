import { useEffect } from "react"
import { useDispatch } from 'react-redux'
import { useGetMenuItemQuery } from '@singleItemButton/services/MenuItem'
import { addMenuItem } from '@singleItemButton/store/slices/itemSlice'
import { __ } from '@wordpress/i18n'

const App = () => {

    let postId = vajofoSingleItemButtonLocalizer.postId

    const storeUrl = vajofoSingleItemButtonLocalizer.storeUrl

    if (storeUrl.length === 0) {
        return null
    }

    const storeLink = storeUrl.endsWith('/') ? storeUrl.slice(0, -1) : storeUrl

    const dispatch = useDispatch()

    const { data, isLoading, error } = useGetMenuItemQuery({ postId: postId || 0 })

    useEffect(() => {
        if (data) {

            dispatch(addMenuItem({ item: data }))
        }
    }, [data, dispatch])

    if (isLoading) {
        return null
    }

    if (error) {
        return null
    }

    return (
        <a className="ofo-single-item-button" href={storeLink+'/#/item/'+data.id}>
            {__('Customize Item', 'olena-food-ordering')}
        </a>
    )
}

export default App
