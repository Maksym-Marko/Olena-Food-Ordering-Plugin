<?php

namespace VAJOFOWPPGNext\Features\API\Routes\AddOnsManager;

use VAJOFOWPPGNext\Features\API\AbstractClasses\AbstractRestRouteHandler;
use WP_REST_Response;
use VAJOFOWPPGNext\Shared\AddOnsManager;
use VAJOFOWPPGNext\Shared\SettingsManager;

class UpdateAddOnRoute extends AbstractRestRouteHandler
{

    protected $route = '/update-add-on';

    public function handleRequest($request): WP_REST_Response
    {

        // Action before processing request
        do_action('ofo_before_handle_update_addon_request', $request);

        // Verify security nonce
        $nonceCheck = $this->verifyNonce($request);
        if ($nonceCheck !== true) {
            return new WP_REST_Response([
                'error' => esc_html__('Invalid Nonce.', 'olena-food-ordering')
            ], 401);
        }

        // Allow modification of required capability
        $requiredCapability = apply_filters(
            'ofo_update_addon_capability',
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
        $newName = $request->get_param('newName');
        $newPrice = $request->get_param('newPrice');

        if (empty($addOnId)) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => esc_html__('Add-on ID is required', 'olena-food-ordering')
            ], 400);
        }

        $post = get_post($addOnId);
        if (!$post || get_post_type($post) !== SettingsManager::ADD_ONS_SLUG) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => esc_html__('Add-on not found', 'olena-food-ordering')
            ], 400);
        }

        if (empty($newName)) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => esc_html__('Add-on name is required', 'olena-food-ordering')
            ], 400);
        }

        if (empty($newPrice)) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => esc_html__('Price is required', 'olena-food-ordering')
            ], 400);
        }

        // Check if price is numeric
        if (!is_numeric($newPrice)) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => esc_html__('Price must be a valid number', 'olena-food-ordering')
            ], 400);
        }

        // Check for negative price
        if ($newPrice < 0) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => esc_html__('Price cannot be negative', 'olena-food-ordering')
            ], 400);
        }

        // Check for reasonable maximum price (e.g., 999999.99)
        if ($newPrice > 999999.99) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => esc_html__('Price exceeds maximum allowed value', 'olena-food-ordering')
            ], 400);
        }

        // Convert to float for proper comparison
        $newPrice = sprintf('%.2f', (float) $newPrice);

        if (strlen($newName) > 200) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => esc_html__('Add-on name is too long (maximum 200 characters)', 'olena-food-ordering')
            ], 400);
        }

        if (!preg_match('/^[\p{L}\p{N}\s\-_]+$/u', $newName)) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => esc_html__('Add-on name contains invalid characters', 'olena-food-ordering')
            ], 400);
        }

        try {

            // Action before updating add-ons
            do_action('ofo_before_update_addon', $addOnId, $newName, $newPrice);

            $updated = AddOnsManager::updateAddOn($addOnId, $newName, $newPrice);

            if (!$updated) {

                return new WP_REST_Response([
                    'status' => 'error',
                    'message' => esc_html__('Failed to update add-on', 'olena-food-ordering')
                ], 500);
            }

            // Action after successful update
            do_action('ofo_after_update_addon_success', $addOnId, $newName, $newPrice);

            return new WP_REST_Response([
                'status'   => 'success',
                'message' => esc_html__('Add-on updated successfully', 'olena-food-ordering')
            ], 200);
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
