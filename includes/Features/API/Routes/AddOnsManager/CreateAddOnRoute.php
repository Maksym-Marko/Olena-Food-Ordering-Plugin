<?php

namespace VAJOFOWPPGNext\Features\API\Routes\AddOnsManager;

use VAJOFOWPPGNext\Features\API\AbstractClasses\AbstractRestRouteHandler;
use WP_REST_Response;
use VAJOFOWPPGNext\Shared\AddOnsManager;
use VAJOFOWPPGNext\Shared\SettingsManager;

class CreateAddOnRoute extends AbstractRestRouteHandler
{

    protected $route = '/create-add-on';

    /**
     * Handles the creation of a new add-on through the REST API.
     * 
     * This endpoint processes requests to create new add-ons, performing extensive validation
     * on the input parameters and enforcing security checks before creation.
     *
     * @param WP_REST_Request $request The incoming request object containing the following parameters:
     *     @type int    $categoryId  The ID of the category to associate the add-on with
     *     @type string $name        The name of the add-on (max 200 characters, alphanumeric with spaces/hyphens/underscores)
     *     @type string $slug        The URL-friendly slug for the add-on (max 200 characters)
     *     @type float  $price       The price of the add-on (between 0 and 999999.99)
     *     @type string $description The description of the add-on
     *
     * @return WP_REST_Response Response object containing status and message
     *
     * @throws Exception If there's an error during add-on creation
     *
     * Response Codes:
     *     200 - Success
     *     400 - Bad Request (validation errors)
     *     401 - Unauthorized (invalid nonce or insufficient permissions)
     *     500 - Server Error
     *
     * Validation Rules:
     * - Name must be:
     *   - Non-empty
     *   - Max 200 characters
     *   - Only letters, numbers, spaces, hyphens, and underscores
     * - Price must be:
     *   - Non-empty
     *   - Numeric
     *   - Non-negative
     *   - Not exceed 999999.99
     * - Description must be non-empty
     * - Slug must be:
     *   - URL-friendly
     *   - Max 200 characters
     *   - Unique within add-ons
     *
     * Actions Fired:
     * - 'ofo_before_handle_addon_create_request' - Before processing request
     * - 'ofo_before_create_addon' - Before creating the add-on
     * - 'ofo_after_create_addon_success' - After successful creation
     *
     * Filters:
     * - 'ofo_addon_create_capability' - Modifies required capability (default: 'edit_posts')
     *
     * Example Success Response:
     * {
     *     "status": "success",
     *     "message": "Add-on created successfully"
     * }
     *
     * Example Error Response:
     * {
     *     "status": "error",
     *     "message": "Add-on name is required"
     * }
     */
    public function handleRequest($request): WP_REST_Response
    {

        // Action before processing request
        do_action('ofo_before_handle_addon_create_request', $request);

        // Verify security nonce
        $nonceCheck = $this->verifyNonce($request);
        if ($nonceCheck !== true) {
            return new WP_REST_Response([
                'error' => esc_html__('Invalid Nonce.', 'olena-food-ordering')
            ], 401);
        }

        // Allow modification of required capability
        $requiredCapability = apply_filters(
            'ofo_addon_create_capability',
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
        $name = $request->get_param('name');
        $slug = $request->get_param('slug');
        $price = $request->get_param('price');
        $description = $request->get_param('description');

        if (empty($name)) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => esc_html__('Add-on name is required', 'olena-food-ordering')
            ], 400);
        }

        if (empty($price)) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => esc_html__('Price is required', 'olena-food-ordering')
            ], 400);
        }

        // Check if price is numeric
        if (!is_numeric($price)) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => esc_html__('Price must be a valid number', 'olena-food-ordering')
            ], 400);
        }

        // Check for negative price
        if ($price < 0) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => esc_html__('Price cannot be negative', 'olena-food-ordering')
            ], 400);
        }

        // Check for reasonable maximum price (e.g., 999999.99)
        if ($price > 999999.99) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => esc_html__('Price exceeds maximum allowed value', 'olena-food-ordering')
            ], 400);
        }

        // Convert to float for proper comparison
        $price = sprintf('%.2f', (float) $price);

        if (strlen($name) > 200) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => esc_html__('Add-on name is too long (maximum 200 characters)', 'olena-food-ordering')
            ], 400);
        }

        if (!preg_match('/^[\p{L}\p{N}\s\-_]+$/u', $name)) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => esc_html__('Add-on name contains invalid characters', 'olena-food-ordering')
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

        // Check if slug already exists
        $args = array(
            'name'        => $slug,
            'post_type'   => SettingsManager::ADD_ONS_SLUG,
            'post_status' => 'publish',
            'numberposts' => 1
        );

        $posts = get_posts($args);

        if (!empty($posts)) {

            return new WP_REST_Response([
                'status' => 'error',
                'message' => esc_html__('An add-on with this slug already exists', 'olena-food-ordering')
            ], 400);
        }

        try {

            // Action before updating add-ons
            do_action('ofo_before_create_addon', $categoryId, $name, $slug, $price, $description);

            $postId = AddOnsManager::createAddOn($categoryId, $name, $slug, $price, $description);

            if (!$postId) {

                return new WP_REST_Response([
                    'status' => 'error',
                    'message' => esc_html__('Failed to create add-on', 'olena-food-ordering')
                ], 500);
            }

            // Action after create successfully
            do_action('ofo_after_create_addon_success', $categoryId, $name, $price, $description);

            // Allow modifications to the final response data
            $responseData = apply_filters(
                'ofo_addon_created_response',
                [
                    'add_on_id' => $postId,
                    'status'   => 'success',
                    'message' => esc_html__('Add-on created successfully', 'olena-food-ordering')
                ],
                $postId
            );

            // Action after processing request, before sending response
            do_action('ofo_after_handle_addon_creation_request', $responseData, $request, $postId);

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
