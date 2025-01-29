<?php

namespace VAJOFOWPPGNext\Features\API\Routes\Menu;

use VAJOFOWPPGNext\Features\API\AbstractClasses\AbstractRestRouteHandler;
use WP_REST_Response;
use VAJOFOWPPGNext\Shared\MenuManager;

class GetMenuItemsRoute extends AbstractRestRouteHandler
{

    protected $route = '/get-menu-items';

    protected $methods = 'GET';

    public function checkPermissions(): bool
    {

        return true;
    }

    public function handleRequest($request): WP_REST_Response
    {

        // Action before processing request
        do_action('ofo_before_handle_get_menu_items_request', $request);

        // Verify security nonce
        $nonceCheck = $this->verifyNonce($request);
        if ($nonceCheck !== true) {
            return new WP_REST_Response([
                'error' => esc_html__('Invalid Nonce.', 'olena-food-ordering')
            ], 401);
        }

        $menuArgs = apply_filters('ofo_menu_manager_properties', [
            'currentPage' => $request->get_param('currentPage'),
            'perPage' => $request->get_param('perPage'),
            'order' => $request->get_param('order')
        ], $request);

        $menuManager = new MenuManager($menuArgs);

        $response = $menuManager->getMenuItems();

        if (!$response) {

            return new WP_REST_Response([
                'message' => esc_html__('Something went wrong with menu items.', 'olena-food-ordering')
            ], 500);
        }

        // Allow modifications to the final response data
        $responseData = apply_filters(
            'ofo_get_menu_items_response',
            $response
        );

        // Action after processing request, before sending response
        do_action('ofo_after_handle_get_menu_items_request', $responseData, $request);

        // Return formatted response
        return new WP_REST_Response($responseData, 200);
    }
}
