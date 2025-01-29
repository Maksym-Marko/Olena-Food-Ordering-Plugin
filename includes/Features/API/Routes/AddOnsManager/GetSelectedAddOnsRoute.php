<?php

namespace VAJOFOWPPGNext\Features\API\Routes\AddOnsManager;

use VAJOFOWPPGNext\Features\API\AbstractClasses\AbstractRestRouteHandler;
use VAJOFOWPPGNext\Shared\AddOnsManager;
use WP_REST_Response;

/**
 * REST API endpoint handler for retrieving available add-ons.
 * 
 * This class extends AbstractRestRouteHandler to provide a GET endpoint that returns
 * a list of available add-ons for the system. It implements security checks through
 * nonce verification and user capability validation.
 *
 * Endpoint: /get-available-add-ons
 * Method: GET
 * Required Capability: edit_posts
 * 
 * @package VAJOFOWPPGNext\Features\API\Routes\AddOnsManager
 */
class GetSelectedAddOnsRoute extends AbstractRestRouteHandler
{

    protected $route = '/get-selected-add-ons/(?P<postId>[\d]+)';

    protected $methods = 'GET';

    /**
     * Handles the REST API request for retrieving available add-ons.
     *
     * Performs the following operations:
     * 1. Validates the request nonce
     * 2. Verifies user has 'edit_posts' capability
     * 3. Retrieves and returns organized add-ons list
     *
     * @param mixed $request The WordPress REST request object
     * @return WP_REST_Response Response containing available add-ons or error message
     *
     * @throws \WP_REST_Response Returns 401 status code for invalid nonce or insufficient permissions
     */
    public function handleRequest($request): WP_REST_Response
    {

        // Action before processing request
        do_action('ofo_before_handle_get_selected_addons_request', $request);

        // Verify security nonce
        $nonceCheck = $this->verifyNonce($request);
        if ($nonceCheck !== true) {
            return new WP_REST_Response([
                'message' => esc_html__('Invalid Nonce.', 'olena-food-ordering')
            ], 401);
        }

        // Allow modification of required capability
        $requiredCapability = apply_filters(
            'ofo_get_selected_addons_capability',
            'edit_posts'
        );

        // Verify user permissions
        $capabilityCheck = $this->verifyUserCapability($requiredCapability);
        if ($capabilityCheck !== true) {
            return new WP_REST_Response([
                'message' => esc_html__('Invalid CapabilityCheck.', 'olena-food-ordering')
            ], 401);
        }

        $postId = $request->get_param('postId');

        if (empty($postId)) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => esc_html__('Post ID is required for getting selected addons', 'olena-food-ordering')
            ], 400);
        }

        if (!get_post($postId)) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => esc_html__('Post not found via getting selected addons', 'olena-food-ordering')
            ], 404);
        }

        // Allow modifications to the selected add-ons before processing
        $selectedAddOns = apply_filters(
            'ofo_selected_addons_pre_get',
            AddOnsManager::getAddOnsPerPost($postId),
            $postId
        );

        // Allow modifications to the final response data
        $responseData = apply_filters(
            'ofo_get_selected_addons_response',
            ['addOns' => $selectedAddOns],
            $postId
        );

        // Action after processing request, before sending response
        do_action('ofo_after_handle_get_selected_addons_request', $responseData, $request, $postId);

        // Return formatted response
        return new WP_REST_Response($responseData, 200);
    }
}
