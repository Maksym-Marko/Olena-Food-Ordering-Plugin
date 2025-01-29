<?php

namespace VAJOFOWPPGNext\Shared;

defined('ABSPATH') || exit;

use WP_REST_Response;
use VAJOFOWPPGNext\Shared\SettingsManager;

class SettingsValidator
{

    /**
     * List of WordPress reserved terms that cannot be used as slugs.
     * These terms are reserved for core WordPress functionality.
     *
     * @var array
     */
    public static $reservedTerms = [
        'post',
        'page',
        'attachment',
        'revision',
        'nav_menu_item',
        'action',
        'author',
        'order',
        'theme',
        'plugin',
        'term',
        'taxonomy',
        'tag',
        'category',
        'public',
        'private',
        'protected',
        'published',
        'draft',
        'pending',
        'trash',
        'upload',
        'media'
    ];

    /**
     * Validate the currency setting
     * 
     * @param string $setting The setting key
     * @param string $value The currency value to validate
     * @return mixed True if valid, WP_REST_Response otherwise
     */
    public static function validate_currency($setting, $value): mixed
    {
        $validCurrencies = array_column(SettingsManager::getPopularCurrencies(), 'value');

        if (!in_array($value, $validCurrencies, true)) {
            return new WP_REST_Response([
                'message' => esc_html__('Invalid currency value. ', 'olena-food-ordering') . $setting
            ], 400);
        }

        return true;
    }

    /**
     * Validate the enable_free_delivery setting
     * 
     * @param string $setting The setting key
     * @param bool $value The value to validate
     * @return mixed True if valid, WP_REST_Response otherwise
     */
    public static function validate_enable_free_delivery($setting, $value): mixed
    {

        if ($value !== 'yes' && $value !== 'no') {
            return new WP_REST_Response([
                'message' => esc_html__('Invalid value. It should be "yes" or "no". ', 'olena-food-ordering') . $setting
            ], 400);
        }

        return true;
    }

    /**
     * Validate the free_delivery_min_amount setting
     * 
     * @param string $setting The setting key
     * @param float $value The value to validate
     * @return mixed True if valid, WP_REST_Response otherwise
     */
    public static function validate_free_delivery_min_amount($setting, $value): mixed
    {
        if (!is_numeric($value) || $value < 0) {
            return new WP_REST_Response([
                'message' => esc_html__('Invalid value. It should be a non-negative number.', 'olena-food-ordering') . $setting
            ], 400);
        }

        return true;
    }

    /**
     * Validate the free_delivery_requirements setting
     * 
     * @param string $setting The setting key
     * @param string $value The value to validate
     * @return mixed True if valid, WP_REST_Response otherwise
     */
    public static function validate_free_delivery_requirements($setting, $value): mixed
    {
        if (!is_string($value) || empty($value)) {
            return new WP_REST_Response([
                'message' => esc_html__('Invalid value. It should be a non-empty string.', 'olena-food-ordering') . $setting
            ], 400);
        }

        return true;
    }

    /**
     * Validate the menu slug
     * 
     * @param string $slug The menu slug to validate
     * @return mixed True if valid, WP_REST_Response otherwise
     */
    public static function validate_menu_slug($setting, $value): mixed
    {

        return self::validateSlug($setting, $value);
    }

    /**
     * Validate the add-ons slug
     * 
     * @param string $slug The add-ons slug to validate
     * @return mixed True if valid, WP_REST_Response otherwise
     */
    public static function validate_add_ons_slug($setting, $value): mixed
    {
        return self::validateSlug($setting, $value);
    }

    /**
     * Validate the taxonomy menu type slug
     * 
     * @param string $slug The taxonomy menu type slug to validate
     * @return mixed True if valid, WP_REST_Response otherwise
     */
    public static function validate_taxonomy_menu_type_slug($setting, $value): mixed
    {
        return self::validateSlug($setting, $value);
    }

    /**
     * Validate the taxonomy menu tag slug
     * 
     * @param string $slug The taxonomy menu tag slug to validate
     * @return mixed True if valid, WP_REST_Response otherwise
     */
    public static function validate_taxonomy_menu_tag_slug($setting, $value): mixed
    {
        return self::validateSlug($setting, $value);
    }

    /**
     * Validate the taxonomy add-on type slug
     * 
     * @param string $slug The taxonomy add-on type slug to validate
     * @return mixed True if valid, WP_REST_Response otherwise
     */
    public static function validate_taxonomy_add_on_type_slug($setting, $value): mixed
    {
        return self::validateSlug($setting, $value);
    }

    /**
     * Validate the slug
     * 
     * @param string $setting The setting key
     * @param string $value The value to validate
     * @return mixed True if valid, WP_REST_Response otherwise
     */
    protected static function validateSlug($setting, $value)
    {

        // Required field validation
        if (empty($value)) {
            return new WP_REST_Response([
                'message' => esc_html__('Slug is required. ', 'olena-food-ordering') . $setting
            ], 400);
        }

        // Validate slug length (1-20 characters)
        if (strlen($value) < 1 || strlen($value) > 20) {
            return new WP_REST_Response([
                'message' => esc_html__('Slug must be between 1 and 20 characters long. ', 'olena-food-ordering') . $setting,
                'data' => [
                    'slug' => $value,
                    'length' => strlen($value)
                ]
            ], 400);
        }

        // Validate slug characters (lowercase alphanumeric, underscores, hyphens)
        if (!preg_match('/^[a-z0-9_-]+$/', $value)) {
            return new WP_REST_Response([
                'message' => $value . esc_html__(' Slug must start with a letter and can only contain lowercase letters, numbers, underscores, and hyphens. ', 'olena-food-ordering') . $setting
            ], 400);
        }

        // Check against WordPress reserved terms
        if (in_array($value, self::$reservedTerms)) {
            return new WP_REST_Response([
                'message' => esc_html__('Slug cannot use WordPress reserved terms. ', 'olena-food-ordering') . $setting
            ], 400);
        }

        // Validate slug doesn't start with number
        if (preg_match('/^[0-9]/', $value)) {
            return new WP_REST_Response([
                'message' => esc_html__('Slug should not start with a number. ', 'olena-food-ordering') . $setting
            ], 400);
        }

        // Check for consecutive underscores
        if (strpos($value, '__') !== false) {
            return new WP_REST_Response([
                'message' => esc_html__('Slug should not contain consecutive underscores. ', 'olena-food-ordering') . $setting
            ], 400);
        }

        // Validate slug doesn't start/end with underscore
        if (substr($value, 0, 1) === '_' || substr($value, -1) === '_') {
            return new WP_REST_Response([
                'message' => esc_html__('Slug should not start or end with an underscore. ', 'olena-food-ordering') . $setting
            ], 400);
        }

        return true;
    }

    /**
     * Validates the checkbox_setting value
     * 
     * Ensures the checkbox_setting contains valid boolean values for 'red' and 'blue' keys
     *
     * @param string $setting The setting key being validated
     * @param mixed $value The value to validate
     * @return true|WP_REST_Response True if valid, WP_REST_Response with error if invalid
     */
    public static function validate_checkbox_setting($setting, $value)
    {

        // Decode JSON string to array
        $decoded = json_decode($value, true);

        // Check if JSON is valid
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new WP_REST_Response([
                'message' => esc_html__('Invalid JSON format for checkbox setting. ', 'olena-food-ordering') . $setting
            ], 400);
        }

        // Validate structure has required keys
        $requiredKeys = ['blue', 'yellow'];

        foreach ($decoded as $key => $value) {
            // Check if key exists in required keys
            if (!in_array($key, $requiredKeys)) {
                return new WP_REST_Response([
                    'message' => esc_html__('Invalid key: ', 'olena-food-ordering') . $key . '. ' . $setting
                ], 400);
            }

            // Validate values are boolean
            if ($value !== null && !is_bool($value)) {
                return new WP_REST_Response([
                    'message' => esc_html__('Value for ', 'olena-food-ordering') . $key . esc_html__(' must be boolean. ', 'olena-food-ordering') . $setting
                ], 400);
            }
        }

        return true;
    }

    /**
     * Validate the items_per_page setting
     * 
     * @param string $setting The setting key
     * @param int $value The value to validate
     * @return mixed True if valid, WP_REST_Response otherwise
     */
    public static function validate_items_per_page($setting, $value)
    {

        // Check if value is numeric and positive
        if (!is_numeric($value) || $value <= 0) {
            return new WP_REST_Response([
                'message' => esc_html__('Items per page must be a positive number. ', 'olena-food-ordering') . $setting
            ], 400);
        }

        // Check if value is a whole number
        if ($value != (int)$value) {
            return new WP_REST_Response([
                'message' => esc_html__('Items per page must be a whole number. ', 'olena-food-ordering') . $setting
            ], 400);
        }

        return true;
    }

    /**
     * Validate the store_url setting
     * 
     * @param string $setting The setting key
     * @param string $value The URL to validate
     * @return mixed True if valid, WP_REST_Response otherwise
     */
    public static function validate_store_url($setting, $value): mixed
    {
        // Check if URL is empty
        if (empty($value)) {
            return new WP_REST_Response([
                'message' => esc_html__('Store URL cannot be empty. ', 'olena-food-ordering') . $setting
            ], 400);
        }

        // Check if URL is valid
        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            return new WP_REST_Response([
                'message' => esc_html__('Invalid store URL format. ', 'olena-food-ordering') . $setting
            ], 400);
        }

        // Check if URL contains reserved terms
        $urlPath = wp_parse_url($value, PHP_URL_PATH);
        if ($urlPath) {
            $pathSegments = array_filter(explode('/', $urlPath));
            foreach ($pathSegments as $segment) {
                if (in_array(strtolower($segment), self::$reservedTerms)) {
                    return new WP_REST_Response([
                        'message' => esc_html__('Store URL contains reserved term: ', 'olena-food-ordering') . $segment . '. ' . $setting
                    ], 400);
                }
            }
        }

        return true;
    }
}
