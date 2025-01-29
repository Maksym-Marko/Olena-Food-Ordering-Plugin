import React from 'react';
import { useParams } from 'react-router-dom';
import { useDispatch, useSelector } from 'react-redux';
import { SingleProduct } from '@olenaStore/components/menu-item/SingleProduct';
import { useEffect } from 'react';
import { selectMenuItem } from '@olenaStore/store/slices/menu-selection/menuSelectionSlice';
import { useNavigate } from 'react-router-dom';
import { useGetMenuItemQuery } from '@olenaStore/services/Menu';
import { addMenuItem } from '@olenaStore/store/slices/menu/menuSlice';

const Item = () => {    

    const dispatch = useDispatch();

    const navigate = useNavigate();

    const { itemId } = useParams();

    const { data: menuItem, isLoading, error } = useGetMenuItemQuery({ postId: itemId })     

    const menuItemsCollection = useSelector(state => state.menu.menuItems)

    useEffect(() => {
        if (error) {
            navigate('/');
        }
    }, [error, navigate]);

    useEffect(() => {

        if(menuItem) {

            const existingItem = menuItemsCollection?.menuItems?.find(item => item.id === menuItem.id)
            
            if (typeof existingItem === 'undefined') {
                
                // add to collection
                dispatch(addMenuItem({item: menuItem}))
            }
        }
    }, [menuItem])   

    const activeMenuItem = menuItemsCollection?.menuItems?.find(item => item.id === parseInt(itemId))    

    const handleCloseMenuItem = () => {
        
        if (window.history.length > 1) {
            if (document.referrer.includes(window.location.origin)) {
                navigate(-1);
            } else {
                navigate('/');
            }
        } else {
            navigate('/');
        }
    }

    useEffect(() => {

        if(activeMenuItem) {
            // Add To selection
            dispatch(selectMenuItem({item: activeMenuItem}))
        }
    }, [activeMenuItem])

    return (
        activeMenuItem && (
            <SingleProduct
                item={activeMenuItem}
                availableAddOns={menuItemsCollection.available_addons}
                handleClose={handleCloseMenuItem}
            />
        )
    );
};

export default Item;
