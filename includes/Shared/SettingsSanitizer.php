<?php

namespace VAJOFOWPPGNext\Shared;

/**
 * Class SettingsSanitizer
 * 
 * Provides sanitization methods for plugin settings.
 */
class SettingsSanitizer
{

    /**
     * Sanitize the free_delivery_requirements setting.
     * 
     * This method adds a filter to sanitize the 'free_delivery_requirements' setting
     * using the sanitize_text_field function. It ensures that the value of the 
     * 'free_delivery_requirements' setting is a clean text string.
     * 
     * @return void
     */
    public static function sanitize_free_delivery_requirements()
    {
        add_filter('ofo_sanitize_setting', function ($sanitized_value, $key, $value) {

            if ($key === 'free_delivery_requirements') {
                return sanitize_text_field($value);
            }

            return $sanitized_value;
        }, 10, 3);
    }

    /**
     * Sanitize the free_delivery_min_amount setting.
     * 
     * This method adds a filter to sanitize the 'free_delivery_min_amount' setting
     * by ensuring it is a non-negative float value.
     * 
     * @return void
     */
    public static function sanitize_free_delivery_min_amount()
    {
        add_filter('ofo_sanitize_setting', function ($sanitized_value, $key, $value) {
            if ($key === 'free_delivery_min_amount') {
                return abs(floatval($value));
            }

            return $sanitized_value;
        }, 10, 3);
    }

    /**
     * Sanitize the checkbox_setting value.
     * 
     * This method adds a filter to sanitize the 'checkbox_setting' by ensuring
     * it contains valid boolean values for 'red' and 'blue' keys.
     *
     * @return void
     */
    public static function sanitize_checkbox_setting()
    {

        add_filter('ofo_sanitize_setting', function ($sanitized_value, $key, $value) {
            if ($key === 'checkbox_setting') {

                $decoded = json_decode($value, true);

                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {

                    return array(
                        'blue' => isset($decoded['blue']) ? (bool)$decoded['blue'] : false,
                        'yellow' => isset($decoded['yellow']) ? (bool)$decoded['yellow'] : false,
                    );
                }

                return array('blue' => true, 'yellow' => true);
            }

            return $sanitized_value;
        }, 10, 3);
    }

    /**
     * Sanitize the items_per_page setting.
     * 
     * This method adds a filter to sanitize the 'items_per_page' setting
     * by ensuring it is a positive integer value.
     * 
     * @return void
     */
    public static function sanitize_items_per_page()
    {

        add_filter('ofo_sanitize_setting', function ($sanitized_value, $key, $value) {
            if ($key === 'items_per_page') {
                $value = absint($value);
                return max(1, $value);
            }

            return $sanitized_value;
        }, 10, 3);
    }

    /**
     * Sanitize the store_url setting.
     * 
     * This method adds a filter to sanitize the 'store_url' setting
     * by ensuring it is a valid URL and sanitizing it.
     * 
     * @return void
     */
    public static function sanitize_store_url()
    {
        add_filter('ofo_sanitize_setting', function ($sanitized_value, $key, $value) {
            if ($key === 'store_url') {
                // Sanitize URL
                $sanitized_url = esc_url_raw(trim($value));
                
                // Remove trailing slash
                return rtrim($sanitized_url, '/');
            }

            return $sanitized_value;
        }, 10, 3);
    }
}
