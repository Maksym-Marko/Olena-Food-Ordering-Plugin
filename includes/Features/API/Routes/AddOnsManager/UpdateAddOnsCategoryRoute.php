<?php

namespace VAJOFOWPPGNext\Features\API\Routes\AddOnsManager;

use VAJOFOWPPGNext\Features\API\AbstractClasses\AbstractRestRouteHandler;
use WP_REST_Response;
use VAJOFOWPPGNext\Shared\AddOnsManager;
use VAJOFOWPPGNext\Shared\SettingsManager;

class UpdateAddOnsCategoryRoute extends AbstractRestRouteHandler
{

    protected $route = '/update-add-ons-category';

    public function handleRequest($request): WP_REST_Response
    {

        // Action before processing request
        do_action('ofo_before_handle_update_addons_category_request', $request);

        // Verify security nonce
        $nonceCheck = $this->verifyNonce($request);
        if ($nonceCheck !== true) {
            return new WP_REST_Response([
                'error' => esc_html__('Invalid Nonce.', 'olena-food-ordering')
            ], 401);
        }

        // Allow modification of required capability
        $requiredCapability = apply_filters(
            'ofo_update_addons_category_capability',
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
        $newName = $request->get_param('newName');
        $newSlug = $request->get_param('newSlug');

        if (empty($categoryId)) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => esc_html__('Category ID is required', 'olena-food-ordering')
            ], 400);
        }

        if (empty($newName)) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => esc_html__('Category Name is required', 'olena-food-ordering')
            ], 400);
        }

        if (strlen($newName) > 200) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => esc_html__('Category name is too long (maximum 200 characters)', 'olena-food-ordering')
            ], 400);
        }

        if (!preg_match('/^[\p{L}\p{N}\s\-_]+$/u', $newName)) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => esc_html__('Category name contains invalid characters', 'olena-food-ordering')
            ], 400);
        }

        // Check if name already exists in other categories
        $existing_term = term_exists($newName, SettingsManager::TAXONOMY_ADD_ON_TYPE_SLUG);
        if ($existing_term && $existing_term['term_id'] != $categoryId) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => esc_html__('A category with this name already exists', 'olena-food-ordering')
            ], 400);
        }

        if (empty($newSlug)) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => esc_html__('Category Slug is required', 'olena-food-ordering')
            ], 400);
        }

        $sanitizedSlug = sanitize_title($newSlug);

        // Check if slug is valid
        if ($sanitizedSlug !== $newSlug) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => esc_html__('Slug contains invalid characters', 'olena-food-ordering')
            ], 400);
        }

        // Check slug length
        if (strlen($newSlug) > 200) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => esc_html__('Slug is too long (maximum 200 characters)', 'olena-food-ordering')
            ], 400);
        }

        // Check if slug already exists
        $existingSlug = get_term_by('slug', $newSlug, SettingsManager::TAXONOMY_ADD_ON_TYPE_SLUG);
        if ($existingSlug && $existingSlug->term_id != $categoryId) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => esc_html__('A category with this slug already exists', 'olena-food-ordering')
            ], 400);
        }

        try {

            // Action before updating add-ons
            do_action('ofo_before_update_addons_category', $categoryId, $newName, $newSlug);

            $updated = AddOnsManager::updateAddOnsCategory($categoryId, $newName, $newSlug);

            if (!$updated) {

                return new WP_REST_Response([
                    'status' => 'error',
                    'message' => esc_html__('Failed to update category', 'olena-food-ordering')
                ], 500);
            }

            // Action after successful update
            do_action('ofo_after_update_addons_category_success', $categoryId, $newName, $newSlug);

            return new WP_REST_Response([
                'status'   => 'success',
                'message' => esc_html__('Category updated successfully', 'olena-food-ordering')
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
