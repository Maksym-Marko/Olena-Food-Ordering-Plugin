import { __ } from '@wordpress/i18n';
import { useDispatch, useSelector } from "react-redux"
import { MenuGridItem } from '@olenaStore/components/MenuGridItem'
import { Pagination } from '@olenaStore/components/Pagination'
import { CartButton } from "@olenaStore/components/CartButton"
import { setCurrentPage } from "@olenaStore/store/slices/menu/menuSlice"
import { useEffect } from "react"
import { useNavigate } from "react-router-dom"

const MainMenu = () => {

    const menuItemsCollection = useSelector(state => state.menu.menuItems)
    const currentPage = useSelector(state => state.menu.currentPage)
    const dispatch = useDispatch()
    const navigate = useNavigate()


    useEffect(() => {
		if(currentPage>1 && menuItemsCollection?.menuItems?.length===0 || currentPage<1) {

            dispatch(setCurrentPage(1))
            navigate(`/page/${1}`)
        }
	}, [menuItemsCollection, currentPage]);
    

    return (
        <>
        <CartButton />

        <div className="ofo-container">
                        
            <div className="menu-grid">
                {menuItemsCollection?.menuItems?.map((item, index) => (
                    <MenuGridItem
                        key={item.id || index}
                        item={item}
                    />
                ))}
            </div>
            {
                (menuItemsCollection?.pagination && menuItemsCollection?.pagination?.total_pages>1) &&
                <Pagination 
                    totalPages={menuItemsCollection.pagination?.total_pages}
                />
            }
            
        </div>
        </>
    )
}

export default MainMenu;