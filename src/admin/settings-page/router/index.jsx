import { createHashRouter } from 'react-router-dom';

// Layouts
import DefaultLayout from "@settings/components/DefaultLayout"

// Pages
import Settings from "@settings/pages/Settings"
import Import from "@settings/pages/Import"
import NotFound from "@settings/pages/NotFound"
import AdditionalInfo from "@settings/pages/AdditionalInfo"

let scalableRoutes = [
    {
        index: true,
        element: <Settings />,
    },
    {
        path: '/import',
        element: <Import />,
    },
    {
        path: '/additional-info',
        element: <AdditionalInfo />,
    }
];

if (window.olenaFoodOrdering?.settingPages?.length > 0) {

    window.olenaFoodOrdering.settingPages.forEach(page => {
        scalableRoutes.push({
            path: page.path,
            element: page.element,
        });
    });
}

const router = createHashRouter([
    {
        path: '/',  // This will be added after the #
        element: <DefaultLayout />,
        children: [
            ...scalableRoutes,
            {
                path: '*',
                element: <NotFound />,
            }
        ]
    }
]);

export default router