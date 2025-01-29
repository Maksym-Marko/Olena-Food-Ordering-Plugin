<?php

namespace VAJOFOWPPGNext\Features\API\Routes\AddOnsManager;

use VAJOFOWPPGNext\Features\API\AbstractClasses\AbstractRestRouteHandler;
use WP_REST_Response;
use VAJOFOWPPGNext\Shared\AddOnsManager;
use VAJOFOWPPGNext\Shared\SettingsManager;

class DeleteAddOnCategoryRoute extends AbstractRestRouteHandler
{

    protected $route = '/delete-add-on-category';

    public function handleRequest($request): WP_REST_Response
    {
        // Action before processing request
        do_action('ofo_before_handle_addons_category_delete_request', $request);

        // Verify security nonce
        $nonceCheck = $this->verifyNonce($request);
        if ($nonceCheck !== true) {
            return new WP_REST_Response([
                'error' => esc_html__('Invalid Nonce.', 'olena-food-ordering')
            ], 401);
        }

        // Allow modification of required capability
        $requiredCapability = apply_filters(
            'ofo_addons_category_delete_capability',
            'edit_posts'
        );

        // Verify user permissions
        $capabilityCheck = $this->verifyUserCapability($requiredCapability);
        if ($capabilityCheck !== true) {
            return new WP_REST_Response([
                'error' => esc_html__('Invalid CapabilityCheck.', 'olena-food-ordering')
            ], 401);
        }

        $categoryId = $request->get_param('categoryId');

        if (empty($categoryId)) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => esc_html__('Category id is required', 'olena-food-ordering')
            ], 400);
        }

        // Check if taxonomy term exists
        $term = get_term($categoryId, SettingsManager::TAXONOMY_ADD_ON_TYPE_SLUG);
        if (is_wp_error($term) || !$term) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => esc_html__('Category not found', 'olena-food-ordering')
            ], 404);
        }

        try {
            // Action before updating add-ons
            do_action('ofo_before_delete_addons_category', $categoryId);

            $deleted = AddOnsManager::deleteAddOnsCategoryAndAddOns($categoryId);

            if (!$deleted) {
                return new WP_REST_Response([
                    'status' => 'error',
                    'message' => esc_html__('Failed to create add-on', 'olena-food-ordering')
                ], 500);
            }

            // Action after delete successfully
            do_action('ofo_after_delete_addon_success', $categoryId);

            // Allow modifications to the final response data
            $responseData = apply_filters(
                'ofo_addons_category_deleted_response',
                [
                    'category_id' => $categoryId,
                    'status'   => 'success',
                    'message' => esc_html__('Add-on deleted successfully', 'olena-food-ordering')
                ],
                $categoryId
            );

            // Action after processing request, before sending response
            do_action('ofo_after_handle_addons_category_delete_request', $responseData, $request, $categoryId);

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
