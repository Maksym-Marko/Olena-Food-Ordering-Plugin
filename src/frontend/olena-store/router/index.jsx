import { createHashRouter } from 'react-router-dom';
// Layouts
import DefaultLayout from "@olenaStore/components/DefaultLayout"

// Pages
import MainMenu from "@olenaStore/pages/MainMenu"
import NotFound from "@olenaStore/pages/NotFound"
import Cart from "@olenaStore/pages/Cart"
import Checkout from "@olenaStore/pages/Checkout"
import Receipt from "@olenaStore/pages/Receipt"
import Item from "@olenaStore/pages/Item"

const router = createHashRouter([
    {
        path: '/',
        element: <DefaultLayout />,
        children: [
            {
                index: true,
                element: <MainMenu />,
            },  
            {
                path: 'page/:page',
                element: <MainMenu />,
            },                 
            {
                path: 'item/:itemId',
                element: <Item />,
            },
            {
                path: 'cart',
                element: <Cart />,
            },
            {
                path: 'checkout',
                element: <Checkout />,
            },
            {
                path: 'receipt',
                element: <Receipt />,
            },
            {
              path: '*',
              element: <NotFound />,
            }
        ]
    }
]);

export default router