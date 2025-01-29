import { createHashRouter } from 'react-router-dom';

// Layouts
import DefaultLayout from "@orderDetails/components/DefaultLayout"

// Pages
import Receipt from "@orderDetails/pages/Receipt"
import NotFound from "@orderDetails/pages/NotFound"

const router = createHashRouter([
    {
        path: '/',
        element: <DefaultLayout />,
        children: [
            {
                index: true,
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