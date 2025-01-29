<?php

defined('ABSPATH') || exit;

// Settings
use VAJOFOWPPGNext\Features\API\Routes\Settings\GetSettingsRoute;
use VAJOFOWPPGNext\Features\API\Routes\Settings\UpdateSettingsRoute;

// AddOns Manager
use VAJOFOWPPGNext\Features\API\Routes\AddOnsManager\GetAvailableAddOnsRoute;
use VAJOFOWPPGNext\Features\API\Routes\AddOnsManager\GetSelectedAddOnsRoute;
use VAJOFOWPPGNext\Features\API\Routes\AddOnsManager\SetSelectedAddOnsRoute;
use VAJOFOWPPGNext\Features\API\Routes\AddOnsManager\UpdateAddOnRoute;
use VAJOFOWPPGNext\Features\API\Routes\AddOnsManager\UpdateAddOnsCategoryRoute;
use VAJOFOWPPGNext\Features\API\Routes\AddOnsManager\CreateAddOnRoute;
use VAJOFOWPPGNext\Features\API\Routes\AddOnsManager\DeleteAddOnRoute;
use VAJOFOWPPGNext\Features\API\Routes\AddOnsManager\CreateAddOnCategoryRoute;
use VAJOFOWPPGNext\Features\API\Routes\AddOnsManager\DeleteAddOnCategoryRoute;

// Demo import
use VAJOFOWPPGNext\Features\API\Routes\DemoImport\DemoImportRoute;
use VAJOFOWPPGNext\Features\API\Routes\DemoImport\GetDemoImportInfoRoute;

// Menu items
use VAJOFOWPPGNext\Features\API\Routes\Menu\GetMenuItemsRoute;
use VAJOFOWPPGNext\Features\API\Routes\Menu\GetMenuItemRoute;

// Order
use VAJOFOWPPGNext\Features\API\Routes\Order\SubmitOrder;

// Receipt
use VAJOFOWPPGNext\Features\API\Routes\Order\GetReceipt;

if (!function_exists('vajofoInitializeRestRoutes')) {
    /**
     * Initialize and register all routes (endpoints).
     */
    function vajofoInitializeRestRoutes()
    {

        $routes = [
            new GetSettingsRoute,
            new UpdateSettingsRoute,
            new GetAvailableAddOnsRoute,
            new SetSelectedAddOnsRoute,
            new GetSelectedAddOnsRoute,
            new UpdateAddOnsCategoryRoute,
            new UpdateAddOnRoute,
            new CreateAddOnRoute,
            new DeleteAddOnRoute,
            new CreateAddOnCategoryRoute,
            new DeleteAddOnCategoryRoute,
            new DemoImportRoute,
            new GetDemoImportInfoRoute,
            new GetMenuItemsRoute,
            new GetMenuItemRoute,
            new SubmitOrder,
            new GetReceipt,
        ];

        foreach ($routes as $route) {

            $route->registerRoute();
        }
    }
}

add_action('rest_api_init', 'vajofoInitializeRestRoutes');
