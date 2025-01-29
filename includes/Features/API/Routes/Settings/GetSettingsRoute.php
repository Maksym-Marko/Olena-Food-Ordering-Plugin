<?php

/**
 * The GetSettingsRoute class handles REST API endpoints for retrieving plugin settings.
 *
 * This class provides a secure endpoint for fetching various plugin settings through 
 * the WordPress REST API, including menu slugs, add-on slugs, and taxonomy slugs.
 * It implements authentication and authorization checks before returning the settings.
 *
 * @package VAJOFOWPPGNext\Features\API\Routes\Settings
 * @since 1.0.0
 */

namespace VAJOFOWPPGNext\Features\API\Routes\Settings;

use VAJOFOWPPGNext\Features\API\AbstractClasses\AbstractRestRouteHandler;
use VAJOFOWPPGNext\Shared\SettingsManager;
use WP_REST_Response;

/**
 * Class GetSettingsRoute
 * 
 * Handles REST API requests for retrieving plugin settings with proper authentication
 * and authorization checks.
 */
class GetSettingsRoute extends AbstractRestRouteHandler
{

    protected $route = '/get-settings';

    protected $methods = 'GET';

    public function checkPermissions(): bool
    {

        return true;
    }

    /**
     * Handles the REST API request to retrieve settings.
     *
     * Processes the incoming request by:
     * 1. Verifying the nonce for security
     * 2. Checking user capabilities
     * 3. Retrieving settings from the SettingsManager
     * 4. Formatting and returning the settings data
     *
     * Each setting is returned with both its label and value for UI display purposes.
     *
     * @param \WP_REST_Request $request The incoming REST request object
     * @return WP_REST_Response Response object containing settings data or error message
     */
    public function handleRequest($request): WP_REST_Response
    {

        // Action before processing request
        do_action('ofo_before_handle_get_settings_request', $request);

        // Verify security nonce
        $nonceCheck = $this->verifyNonce($request);
        if ($nonceCheck !== true) {
            return new WP_REST_Response([
                'error' => esc_html__('Invalid Nonce.', 'olena-food-ordering')
            ], 401);
        }

        // Allow modification of required capability
        $requiredCapability = apply_filters(
            'ofo_get_settings_capability',
            'edit_posts'
        );

        // Allow modifications to settings before processing
        $settings = apply_filters('ofo_settings_pre_get', [

            'store_settings' => [
                'label' => esc_html__('Store Settings', 'olena-food-ordering'),
                'type' => 'section_divider',
            ],

            'currency' => [
                'label' => esc_html__('Currency', 'olena-food-ordering'),
                'value' => SettingsManager::getCurrency(),
                'type' => 'select',
                'options' => SettingsManager::getPopularCurrencies(),
            ],

            'enable_free_delivery' => [
                'label' => esc_html__('Enable Free Delivery', 'olena-food-ordering'),
                'value' => SettingsManager::getEnableFreeDelivery(),
                'type' => 'radio',
                'options' => [
                    [
                        'value' => 'yes',
                        'label' => esc_html__('Yes', 'olena-food-ordering'),
                    ],
                    [
                        'value' => 'no',
                        'label' => esc_html__('No', 'olena-food-ordering'),
                    ]
                ],
            ],

            'free_delivery_min_amount' => [
                'label' => esc_html__('Free Delivery Minimum Amount', 'olena-food-ordering'),
                'value' => SettingsManager::getFreeDeliveryMinAmount(),
                'type' => 'number',
            ],

            'free_delivery_requirements' => [
                'label' => esc_html__('Free Delivery Requirements', 'olena-food-ordering'),
                'value' => SettingsManager::getFreeDeliveryRequirements(),
                'type' => 'text',
            ],

            'items_per_page' => [
                'label' => esc_html__('Items Per Page', 'olena-food-ordering'),
                'value' => SettingsManager::getItemsPerPage(),
                'type' => 'number',
            ],

            'store_url' => [
                'label' => esc_html__('Store URL', 'olena-food-ordering'),
                'value' => SettingsManager::getStoreUrl(),
                'type' => 'url',
            ],

            'permalink_settings' => [
                'label' => esc_html__('Permalink Settings', 'olena-food-ordering'),
                'type' => 'section_divider',
            ],

            'menu_slug' => [
                'label' => esc_html__('Menu Item BaseSlug', 'olena-food-ordering'),
                'value' => SettingsManager::getMenuSlug(),
            ],
            'add_ons_slug' => [
                'label' => esc_html__('Add-ons Base Slug', 'olena-food-ordering'),
                'value' => SettingsManager::getAddOnsSlug(),
            ],
            'taxonomy_menu_type_slug' => [
                'label' => esc_html__('Taxonomy Menu Slug', 'olena-food-ordering'),
                'value' => SettingsManager::getTaxonomyMenuTypeSlug(),
            ],
            'taxonomy_menu_tag_slug' => [
                'label' => esc_html__('Taxonomy Menu Tag Slug', 'olena-food-ordering'),
                'value' => SettingsManager::getTaxonomyMenuTagSlug(),
            ],
            'taxonomy_add_on_type_slug' => [
                'label' => esc_html__('Taxonomy Add-on Type Slug', 'olena-food-ordering'),
                'value' => SettingsManager::getTaxonomyAddOnTypeSlug(),
            ],

            /*
            // An example of how to add a checkbox setting
            'checkbox_setting' => [
                'label' => esc_html__('Test Setting', 'olena-food-ordering'),
                'type' => 'checkbox',
                'value' => SettingsManager::getCheckboxSetting(),
                'options' => [
                    [
                        'value' => 'blue',
                        'label' => esc_html__('Blue', 'olena-food-ordering')
                    ],
                    [
                        'value' => 'yellow',
                        'label' => esc_html__('Yellow', 'olena-food-ordering')
                    ]
                ]
            ],*/

        ]);

        // Allow modifications to the final response data
        $responseData = apply_filters(
            'ofo_settings_response',
            $settings
        );

        // Action after processing request, before sending response
        do_action('ofo_after_handle_get_settings_request', $responseData, $request);

        // Return formatted response
        return new WP_REST_Response($responseData, 200);
    }
}
