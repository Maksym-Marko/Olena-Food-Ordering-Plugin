<?php

/**
 * The UpdateSettingsRoute class handles REST API endpoints for updating plugin settings.
 *
 * This class provides a secure endpoint for updating plugin settings through WordPress REST API.
 * It includes comprehensive validation of slugs and other setting values to ensure data integrity
 * and WordPress compatibility.
 *
 * @package VAJOFOWPPGNext\Features\API\Routes\Settings
 * @since 1.0.0
 */

namespace VAJOFOWPPGNext\Features\API\Routes\Settings;

use VAJOFOWPPGNext\Features\API\AbstractClasses\AbstractRestRouteHandler;
use VAJOFOWPPGNext\Shared\SettingsManager;
use VAJOFOWPPGNext\Shared\Exceptions\SettingsNoChangesException;
use WP_REST_Response;

/**
 * Class UpdateSettingsRoute
 * 
 * Handles REST API requests for updating plugin settings with comprehensive validation.
 */
class UpdateSettingsRoute extends AbstractRestRouteHandler
{

    protected $route = '/update-settings';

    /**
     * Handles the REST API request to update settings.
     *
     * Processes the incoming request by:
     * 1. Verifying the nonce for security
     * 2. Checking user capabilities
     * 3. Validating all setting values
     * 4. Updating the settings in the database
     *
     * @param \WP_REST_Request $request The incoming REST request object
     * @return WP_REST_Response Response object with success/error message and status code
     */
    public function handleRequest($request): WP_REST_Response
    {

        do_action('ofo_before_handle_update_settings_request', $request);

        // Verify security nonce
        $nonceCheck = $this->verifyNonce($request);
        if ($nonceCheck !== true) {
            return new WP_REST_Response([
                'error' => esc_html__('Invalid Nonce.', 'olena-food-ordering')
            ], 401);
        }

        $requiredCapability = apply_filters(
            'ofo_update_settings_capability',
            'edit_posts'
        );

        // Verify user permissions
        $capabilityCheck = $this->verifyUserCapability($requiredCapability);
        if ($capabilityCheck !== true) {
            return new WP_REST_Response([
                'error' => esc_html__('Invalid CapabilityCheck.', 'olena-food-ordering')
            ], 401);
        }

        $settings = $request->get_param('settings');

        $regSettings = apply_filters(
            'ofo_registered_settings_list',
            array_keys(SettingsManager::getDefaultSettings())
        );

        // Validate each setting
        foreach ($regSettings as $setting) {

            if (!isset($settings[$setting])) continue;

            do_action('ofo_before_validate_setting', $setting, $settings[$setting]);

            $validationResult = SettingsManager::validateSettingByKey($setting, $settings[$setting]);

            if ($validationResult instanceof WP_REST_Response) {
                return $validationResult;
            }

            // Sanitize the setting
            SettingsManager::sanitizeSettingByKey($setting);
        }

        if (isset($settings[$setting])) {

            do_action('ofo_after_validate_setting', $setting, $settings[$setting]);
        }

        // Attempt to update settings
        try {

            do_action('ofo_before_update_settings', $settings);

            $updated = apply_filters(
                'ofo_update_settings',
                SettingsManager::updateAllSettings($settings),
                $settings
            );

            if ($updated instanceof WP_REST_Response) {
                return $updated;
            }

            if (!$updated) {
                return new WP_REST_Response([
                    'message' => esc_html__('Failed to update settings in database', 'olena-food-ordering')
                ], 500);
            }

            do_action('ofo_after_update_settings_success', $settings);

            return new WP_REST_Response([
                'status'   => 'success',
                'message' => esc_html__('Settings updated successfully. Please update Permalink Settings, if needed.', 'olena-food-ordering')
            ], 200);
        } catch (\InvalidArgumentException $e) {

            do_action('ofo_settings_invalid_argument_exception', $e, $settings);

            return new WP_REST_Response([
                'message' => esc_html__('Invalid settings provided.', 'olena-food-ordering') . ' ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {

            do_action('ofo_settings_general_exception', $e, $settings);

            return new WP_REST_Response([
                'message' => esc_html__('Something went wrong with settings update', 'olena-food-ordering'),
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
