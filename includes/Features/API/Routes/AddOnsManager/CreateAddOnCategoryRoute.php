<?php

namespace VAJOFOWPPGNext\Features\API\Routes\AddOnsManager;

use VAJOFOWPPGNext\Features\API\AbstractClasses\AbstractRestRouteHandler;
use WP_REST_Response;
use VAJOFOWPPGNext\Shared\AddOnsManager;
use VAJOFOWPPGNext\Shared\SettingsManager;

class CreateAddOnCategoryRoute extends AbstractRestRouteHandler
{

    protected $route = '/create-add-on-category';

    public function handleRequest($request): WP_REST_Response
    {

        // Action before processing request
        do_action('ofo_before_handle_addon_category_create_request', $request);

        // Verify security nonce
        $nonceCheck = $this->verifyNonce($request);
        if ($nonceCheck !== true) {
            return new WP_REST_Response([
                'error' => esc_html__('Invalid Nonce.', 'olena-food-ordering')
            ], 401);
        }

        // Allow modification of required capability
        $requiredCapability = apply_filters(
            'ofo_addon_category_create_capability',
            'edit_posts'
        );

        // Verify user permissions
        $capabilityCheck = $this->verifyUserCapability($requiredCapability);
        if ($capabilityCheck !== true) {
            return new WP_REST_Response([
                'error' => esc_html__('Invalid CapabilityCheck.', 'olena-food-ordering')
            ], 401);
        }

        $name = $request->get_param('name');
        $slug = $request->get_param('slug');
        $description = $request->get_param('description');

        if (empty($name)) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => esc_html__('Add-on category name is required', 'olena-food-ordering')
            ], 400);
        }

        if (strlen($name) > 200) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => esc_html__('Category name is too long (maximum 200 characters)', 'olena-food-ordering')
            ], 400);
        }

        if (!preg_match('/^[\p{L}\p{N}\s\-_]+$/u', $name)) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => esc_html__('Category name contains invalid characters', 'olena-food-ordering')
            ], 400);
        }

        $sanitizedSlug = sanitize_title($slug);

        // Check if slug is valid
        if ($sanitizedSlug !== $slug) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => esc_html__('Slug contains invalid characters', 'olena-food-ordering')
            ], 400);
        }

        // Check slug length
        if (strlen($slug) > 200) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => esc_html__('Slug is too long (maximum 200 characters)', 'olena-food-ordering')
            ], 400);
        }

        // Check if category slug already exists
        // Check if name already exists in other categories
        $existing_term = term_exists($name, SettingsManager::TAXONOMY_ADD_ON_TYPE_SLUG);
        if ($existing_term) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => esc_html__('A category with this name already exists', 'olena-food-ordering')
            ], 400);
        }

        // Check if slug already exists
        $existingSlug = get_term_by('slug', $slug, SettingsManager::TAXONOMY_ADD_ON_TYPE_SLUG);
        if ($existingSlug) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => esc_html__('A category with this slug already exists', 'olena-food-ordering')
            ], 400);
        }

        try {

            // Action before updating add-ons
            do_action('ofo_before_create_addon_category', $name, $slug, $description);

            $categoryId = AddOnsManager::createAddOnCategory($name, $slug, $description);

            if (!$categoryId) {

                return new WP_REST_Response([
                    'status' => 'error',
                    'message' => esc_html__('Failed to create add-on category', 'olena-food-ordering')
                ], 500);
            }

            // Action after create successfully
            do_action('ofo_after_create_addon_category_success', $name, $description);

            // Allow modifications to the final response data
            $responseData = apply_filters(
                'ofo_addon_category_created_response',
                [
                    'category_id' => $categoryId,
                    'status'   => 'success',
                    'message' => esc_html__('Add-on category created successfully', 'olena-food-ordering')
                ],
                $categoryId
            );

            // Action after processing request, before sending response
            do_action('ofo_after_handle_addon_category_creation_request', $responseData, $request, $categoryId);

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
