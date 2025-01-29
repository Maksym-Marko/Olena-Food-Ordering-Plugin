<?php

namespace VAJOFOWPPGNext\Features\API\Routes\AddOnsManager;

use VAJOFOWPPGNext\Features\API\AbstractClasses\AbstractRestRouteHandler;
use WP_REST_Response;
use VAJOFOWPPGNext\Shared\AddOnsManager;
use VAJOFOWPPGNext\Shared\Exceptions\AddOnsNoChangesException;

/**
 * Handles REST API requests to update selected add-ons for a post.
 * 
 * This route handler processes requests to modify the add-ons associated with a specific post.
 * It includes security checks and validation of input parameters.
 */
class SetSelectedAddOnsRoute extends AbstractRestRouteHandler
{

    protected $route = '/set-selected-add-ons';

    /**
     * Handles the REST API request to update selected add-ons.
     *
     * @param \WP_REST_Request $request The WordPress REST request object.
     * @return WP_REST_Response Response containing status and message.
     *
     * @throws AddOnsNoChangesException When the provided add-ons are identical to existing ones.
     * @throws \InvalidArgumentException When invalid add-ons data is provided.
     * @throws \Exception For general errors during the update process.
     *
     * Expected Request Parameters:
     * - selectedAddons: Array of add-ons to be associated with the post
     * - postId: Integer ID of the post to update
     *
     * Response Codes:
     * - 200: Success
     * - 400: Bad Request (invalid input, no changes needed)
     * - 401: Unauthorized (invalid nonce or insufficient permissions)
     * - 404: Post not found
     * - 500: Server error during update
     */
    public function handleRequest($request): WP_REST_Response
    {

        // Action before processing request
        do_action('ofo_before_handle_set_selected_addons_request', $request);

        // Verify security nonce
        $nonceCheck = $this->verifyNonce($request);
        if ($nonceCheck !== true) {
            return new WP_REST_Response([
                'error' => esc_html__('Invalid Nonce.', 'olena-food-ordering')
            ], 401);
        }

        // Allow modification of required capability
        $requiredCapability = apply_filters(
            'ofo_set_selected_addons_capability',
            'edit_posts'
        );

        // Verify user permissions
        $capabilityCheck = $this->verifyUserCapability($requiredCapability);
        if ($capabilityCheck !== true) {
            return new WP_REST_Response([
                'error' => esc_html__('Invalid CapabilityCheck.', 'olena-food-ordering')
            ], 401);
        }

        $selectedAddons = $request->get_param('selectedAddons');

        $postId = $request->get_param('postId');

        if (empty($postId)) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => esc_html__('Post ID is required', 'olena-food-ordering')
            ], 400);
        }

        if (!get_post($postId)) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => esc_html__('Post not found', 'olena-food-ordering')
            ], 404);
        }

        try {

            // Action before updating add-ons
            do_action('ofo_before_update_selected_addons', $postId, $selectedAddons);

            $updated = apply_filters(
                'ofo_update_selected_addons',
                AddOnsManager::updateAddOns($postId, $selectedAddons),
                $postId,
                $selectedAddons
            );

            if ($updated instanceof WP_REST_Response) {
                return $updated;
            }

            if (!$updated) {
                return new WP_REST_Response([
                    'status' => 'error',
                    'message' => esc_html__('Failed to set add-ons', 'olena-food-ordering')
                ], 500);
            }

            // Action after successful update
            do_action('ofo_after_update_selected_addons_success', $postId, $selectedAddons);

            return new WP_REST_Response([
                'status'   => 'success',
                'message' => esc_html__('Add-ons saved successfully', 'olena-food-ordering')
            ], 200);
        } catch (AddOnsNoChangesException $e) {

            do_action('ofo_after_update_selected_addons_no_changes', $e, $postId, $selectedAddons);

            return new WP_REST_Response([
                'status' => 'error',
                'message' => esc_html__('Add-ons are up to date', 'olena-food-ordering')
            ], 400);
        } catch (\InvalidArgumentException $e) {

            do_action('ofo_after_update_selected_addons_invalid_argument_exception', $e, $postId, $selectedAddons);

            return new WP_REST_Response([
                'message' => esc_html__('Invalid add-ons provided', 'olena-food-ordering'),
                'error' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {

            do_action('ofo_after_update_selected_addons_general_exception', $e, $postId, $selectedAddons);

            return new WP_REST_Response([
                'message' => esc_html__('Something went wrong with add-ons update', 'olena-food-ordering'),
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
