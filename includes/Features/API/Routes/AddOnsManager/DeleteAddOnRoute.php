<?php

namespace VAJOFOWPPGNext\Features\API\Routes\AddOnsManager;

use VAJOFOWPPGNext\Features\API\AbstractClasses\AbstractRestRouteHandler;
use WP_REST_Response;
use VAJOFOWPPGNext\Shared\AddOnsManager;
use VAJOFOWPPGNext\Shared\SettingsManager;

/**
 * Handles REST API requests for deleting add-ons in WordPress.
 *
 * This class processes REST API requests to delete add-ons, including security checks,
 * permission verification, and proper error handling. It extends AbstractRestRouteHandler
 * to maintain consistent API handling patterns.
 *
 * @since 1.0.0
 * @package VAJOFOWPPGNext\Features\API\Routes\AddOnsManager
 */
class DeleteAddOnRoute extends AbstractRestRouteHandler
{

    protected $route = '/delete-add-on';

    /**
     * Handles the REST API request to delete an add-on.
     *
     * Processes the deletion request with the following steps:
     * 1. Verifies security nonce
     * 2. Checks user capabilities
     * 3. Validates add-on existence
     * 4. Performs deletion
     * 5. Returns appropriate response
     *
     * @since 1.0.0
     *
     * @param \WP_REST_Request $request The REST request object.
     * @return WP_REST_Response The REST response object.
     *
     * @throws \Exception When an unexpected error occurs during processing.
     *
     * @uses verifyNonce()           For security verification
     * @uses verifyUserCapability()  For permission checking
     * @uses get_post()             To verify add-on existence
     * @uses AddOnsManager::deleteAddOn() To perform the deletion
     *
     * Actions:
     * - 'ofo_before_handle_addon_delete_request': Before processing starts
     * - 'ofo_before_delete_addon': Before deletion
     * - 'ofo_after_delete_addon_success': After successful deletion
     * - 'ofo_after_handle_addon_delete_request': Before sending response
     *
     * Filters:
     * - 'ofo_addon_delete_capability': Modifies required capability
     * - 'ofo_addon_deleted_response': Modifies success response data
     *
     * @example
     * ```php
     * // Example REST API request
     * $response = wp_remote_post(rest_url('/delete-add-on'), [
     *     'headers' => [
     *         'X-WP-Nonce' => wp_create_nonce('wp_rest')
     *     ],
     *     'body' => [
     *         'addOnId' => 123
     *     ]
     * ]);
     * ```
     */
    public function handleRequest($request): WP_REST_Response
    {

        // Action before processing request
        do_action('ofo_before_handle_addon_delete_request', $request);

        // Verify security nonce
        $nonceCheck = $this->verifyNonce($request);
        if ($nonceCheck !== true) {
            return new WP_REST_Response([
                'error' => esc_html__('Invalid Nonce.', 'olena-food-ordering')
            ], 401);
        }

        // Allow modification of required capability
        $requiredCapability = apply_filters(
            'ofo_addon_delete_capability',
            'edit_posts'
        );

        // Verify user permissions
        $capabilityCheck = $this->verifyUserCapability($requiredCapability);
        if ($capabilityCheck !== true) {
            return new WP_REST_Response([
                'error' => esc_html__('Invalid CapabilityCheck.', 'olena-food-ordering')
            ], 401);
        }

        $addOnId = $request->get_param('addOnId');

        if (empty($addOnId)) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => esc_html__('Add-on id is required', 'olena-food-ordering')
            ], 400);
        }

        $post = get_post($addOnId);

        // If the post exists and matches our criteria, return error
        if (
            !$post ||
            $post->post_type !== SettingsManager::ADD_ONS_SLUG ||
            $post->post_status !== 'publish'
        ) {

            return new WP_REST_Response([
                'status'  => 'error',
                'message' => esc_html__('An add-on with this id does not exists', 'olena-food-ordering')
            ], 400);
        }

        try {

            // Action before updating add-ons
            do_action('ofo_before_delete_addon', $addOnId);

            $deleted = AddOnsManager::deleteAddOn($addOnId);

            if (!$deleted) {

                return new WP_REST_Response([
                    'status' => 'error',
                    'message' => esc_html__('Failed to create add-on', 'olena-food-ordering')
                ], 500);
            }

            // Action after delete successfully
            do_action('ofo_after_delete_addon_success', $addOnId);

            // Allow modifications to the final response data
            $responseData = apply_filters(
                'ofo_addon_deleted_response',
                [
                    'add_on_id' => $addOnId,
                    'status'   => 'success',
                    'message' => esc_html__('Add-on deleted successfully', 'olena-food-ordering')
                ],
                $addOnId
            );

            // Action after processing request, before sending response
            do_action('ofo_after_handle_addon_delete_request', $responseData, $request, $addOnId);

            // Return formatted response
            return new WP_REST_Response($responseData, 200);
        } catch (\Exception $e) {

            // Return error response
            return new WP_REST_Response([
                'status' => 'error',
                'message' => $e->getMessage(),
                'code' => $e->getCode() ?: 500
            ], $e->getCode() ?: 500);
        }
    }
}
