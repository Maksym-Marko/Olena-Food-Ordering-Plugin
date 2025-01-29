import { createHashRouter } from 'react-router-dom';
// Layouts
import DefaultLayout from "@addOnsManager/components/DefaultLayout"

// Pages
import AddOnsManager from "@addOnsManager/pages/AddOnsManager"
import NotFound from "@addOnsManager/pages/NotFound"


const router = createHashRouter([
    {
        path: '/',
        element: <DefaultLayout />,
        children: [
            {
                index: true,
                element: <AddOnsManager />,
            },
            {
              path: '*',
              element: <NotFound />,
            }
        ]
    }
]);

export default router