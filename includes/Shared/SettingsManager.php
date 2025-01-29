<?php

/**
 * The SettingsManager class.
 *
 * This class registers helps to 
 * manage website settings
 */

namespace VAJOFOWPPGNext\Shared;

defined('ABSPATH') || exit;

use VAJOFOWPPGNext\Shared\SettingsValidator;
use WP_REST_Response;

class SettingsManager
{

    /**
     * The menu CPT slug.
     */
    const MENU_SLUG = 'olena-menu';

    /**
     * The add-ons CPT slug.
     */
    const ADD_ONS_SLUG = 'olena-menu-add-ons';

    /**
     * The orders CPT slug.
     */
    const ORDERS_SLUG = 'olena-orders';

    /**
     * Taxonomy slug for categorizing menu items by type.
     * Used in WordPress custom taxonomy registration.
     */
    const TAXONOMY_MENU_TYPE_SLUG = 'olena-menu-type';

    /**
     * Taxonomy slug for tagging menu items.
     * Used in WordPress custom taxonomy registration.
     */
    const TAXONOMY_MENU_TAG_SLUG = 'olena-menu-tag';

    /**
     * Taxonomy slug for categorizing add-on items by type.
     * Used in WordPress custom taxonomy registration.
     */
    const TAXONOMY_ADD_ON_TYPE_SLUG = 'olena-add-on-types';

    /**
     * Default settings
     * @var array
     */
    protected static $defaults = [
        'currency'                  => 'usd',
        'enable_free_delivery'       => 'yes',
        'free_delivery_min_amount'   => 50,
        'free_delivery_requirements' => 'Free delivery for orders over $50',
        'items_per_page'             => 9,
        'store_url'                  => '',
        'menu_slug'                  => self::MENU_SLUG,
        'add_ons_slug'               => self::ADD_ONS_SLUG,
        'orders_slug'                => self::ORDERS_SLUG,
        'taxonomy_menu_type_slug'    => self::TAXONOMY_MENU_TYPE_SLUG,
        'taxonomy_menu_tag_slug'     => self::TAXONOMY_MENU_TAG_SLUG,
        'taxonomy_add_on_type_slug'  => self::TAXONOMY_ADD_ON_TYPE_SLUG,

        // An example of how to add a checkbox setting
        'checkbox_setting' => [
            'blue' => true,
            'yellow' => true
        ],
    ];

    /**
     * Set validation rules for each setting
     *
     * @param string $setting The setting key
     * @param callable $callback The validation callback function
     * @return void
     */
    public static function validateSettingByKey($setting, $value)
    {

        $validationMethod = apply_filters("ofo_validation_method_{$setting}", [SettingsValidator::class, "validate_$setting"]);

        if (!is_callable($validationMethod)) {

            return new WP_REST_Response([
                'status' => 'error',
                'message' => esc_html__('The provided callback', 'olena-food-ordering') . ' SettingsValidator::validate_' . $setting . esc_html__('is not callable.', 'olena-food-ordering')
            ], 400);
        }

        return $validationMethod($setting, $value);
    }

    /**
     * Sanitize a setting using the registered sanitization rule
     *
     * @param string $setting The setting key
     * @return mixed The sanitized value
     */
    public static function sanitizeSettingByKey($setting)
    {
        $sanitizationMethod = apply_filters("ofo_sanitization_method_{$setting}", [SettingsSanitizer::class, "sanitize_$setting"]);

        if (is_callable($sanitizationMethod)) {

            $sanitizationMethod();
        }
    }

    /**
     * Validate a setting using the registered validation rule
     *
     * @param string $setting The setting key
     * @param mixed $value The setting value
     * @return bool True if valid, false otherwise
     */
    public static function validateSetting($setting, $value)
    {
        $isValid = apply_filters("ofo_validate_{$setting}", true, $value);

        if ($isValid !== true) {
            throw new \InvalidArgumentException(
                sprintf(
                    esc_html__('Invalid value for setting: ', 'olena-food-ordering') . '%s',
                    esc_html($setting)
                )
            );
        }

        return true;
    }

    /**
     * The option name in WordPress database
     * @var string
     */
    protected static $settingsKey = 'olena_food_ordering_settings';

    /**
     * Get list of all available settings
     * 
     * @return array
     */
    public static function getDefaultSettings()
    {

        return apply_filters(
            'ofo_settings_list',
            self::$defaults
        );
    }

    /**
     * Get Currency setting
     * 
     * @return string
     */
    public static function getCurrency()
    {

        return apply_filters(
            'ofo_get_currency',
            self::getSetting('currency')
        );
    }

    /**
     * Get Enable Free Delivery setting
     * 
     * @return bool
     */
    public static function getEnableFreeDelivery()
    {
        return apply_filters(
            'ofo_get_enable_free_delivery',
            self::getSetting('enable_free_delivery')
        );
    }

    /**
     * Get Free Delivery Minimum Amount setting
     * 
     * @return float
     */
    public static function getFreeDeliveryMinAmount()
    {
        return apply_filters(
            'ofo_get_free_delivery_min_amount',
            self::getSetting('free_delivery_min_amount')
        );
    }

    /**
     * Get Items Per Page setting
     * 
     * @return int
     */
    public static function getItemsPerPage()
    {
        return apply_filters(
            'ofo_get_items_per_page',
            self::getSetting('items_per_page')
        );
    }

    /**
     * Get Store URL setting
     * 
     * @return string
     */
    public static function getStoreUrl()
    {
        return apply_filters(
            'ofo_get_store_url',
            self::getSetting('store_url')
        );
    }

    /**
     * Get Free Delivery Requirements setting
     * 
     * @return string
     */
    public static function getFreeDeliveryRequirements()
    {

        return apply_filters(
            'ofo_get_free_delivery_requirements',
            self::getSetting('free_delivery_requirements')
        );
    }

    /**
     * Get list of 20 most popular currencies
     * 
     * @return array
     */
    public static function getPopularCurrencies()
    {
        return apply_filters('ofo_get_popular_currencies', [
            ['value' => 'usd', 'code' => 'USD', 'label' => __('United States Dollar', 'olena-food-ordering'), 'symbol' => '$'],
            ['value' => 'uah', 'code' => 'UAH', 'label' => __('Ukrainian Hryvnia', 'olena-food-ordering'), 'symbol' => '₴'],
            ['value' => 'eur', 'code' => 'EUR', 'label' => __('Euro', 'olena-food-ordering'), 'symbol' => '€'],
            ['value' => 'jpy', 'code' => 'JPY', 'label' => __('Japanese Yen', 'olena-food-ordering'), 'symbol' => '¥'],
            ['value' => 'gbp', 'code' => 'GBP', 'label' => __('British Pound Sterling', 'olena-food-ordering'), 'symbol' => '£'],
            ['value' => 'aud', 'code' => 'AUD', 'label' => __('Australian Dollar', 'olena-food-ordering'), 'symbol' => 'A$'],
            ['value' => 'cad', 'code' => 'CAD', 'label' => __('Canadian Dollar', 'olena-food-ordering'), 'symbol' => 'C$'],
            ['value' => 'chf', 'code' => 'CHF', 'label' => __('Swiss Franc', 'olena-food-ordering'), 'symbol' => 'CHF'],
            ['value' => 'cny', 'code' => 'CNY', 'label' => __('Chinese Yuan', 'olena-food-ordering'), 'symbol' => '¥'],
            ['value' => 'sek', 'code' => 'SEK', 'label' => __('Swedish Krona', 'olena-food-ordering'), 'symbol' => 'kr'],
            ['value' => 'nzd', 'code' => 'NZD', 'label' => __('New Zealand Dollar', 'olena-food-ordering'), 'symbol' => 'NZ$'],
            ['value' => 'mxn', 'code' => 'MXN', 'label' => __('Mexican Peso', 'olena-food-ordering'), 'symbol' => '$'],
            ['value' => 'sgd', 'code' => 'SGD', 'label' => __('Singapore Dollar', 'olena-food-ordering'), 'symbol' => 'S$'],
            ['value' => 'hkd', 'code' => 'HKD', 'label' => __('Hong Kong Dollar', 'olena-food-ordering'), 'symbol' => 'HK$'],
            ['value' => 'nok', 'code' => 'NOK', 'label' => __('Norwegian Krone', 'olena-food-ordering'), 'symbol' => 'kr'],
            ['value' => 'krw', 'code' => 'KRW', 'label' => __('South Korean Won', 'olena-food-ordering'), 'symbol' => '₩'],
            ['value' => 'try', 'code' => 'TRY', 'label' => __('Turkish Lira', 'olena-food-ordering'), 'symbol' => '₺'],
            ['value' => 'inr', 'code' => 'INR', 'label' => __('Indian Rupee', 'olena-food-ordering'), 'symbol' => '₹'],
            ['value' => 'brl', 'code' => 'BRL', 'label' => __('Brazilian Real', 'olena-food-ordering'), 'symbol' => 'R$'],
            ['value' => 'zar', 'code' => 'ZAR', 'label' => __('South African Rand', 'olena-food-ordering'), 'symbol' => 'R'],
        ]);
    }

    /**
     * Get Add-ons slug
     * 
     * @return string
     */
    public static function getAddOnsSlug()
    {

        return apply_filters(
            'ofo_get_add_ons_slug',
            self::getSetting('add_ons_slug')
        );
    }

    /**
     * Get Menu slug
     * 
     * @return string
     */
    public static function getMenuSlug()
    {

        return apply_filters(
            'ofo_get_menu_slug',
            self::getSetting('menu_slug')
        );
    }

    /**
     * Get Add-on Type taxonomy slug
     * 
     * @return string
     */
    public static function getTaxonomyAddOnTypeSlug()
    {

        return apply_filters(
            'ofo_get_taxonomy_add_on_type_slug',
            self::getSetting('taxonomy_add_on_type_slug')
        );
    }

    /**
     * Get Menu Tag taxonomy slug
     * 
     * @return string
     */
    public static function getTaxonomyMenuTagSlug()
    {

        return apply_filters(
            'ofo_get_taxonomy_menu_tag_slug',
            self::getSetting('taxonomy_menu_tag_slug')
        );
    }

    /**
     * Get Menu Type taxonomy slug
     * 
     * @return string
     */
    public static function getTaxonomyMenuTypeSlug()
    {

        return apply_filters(
            'ofo_get_taxonomy_menu_type_slug',
            self::getSetting('taxonomy_menu_type_slug')
        );
    }

    /**
     * Get Orders slug
     * 
     * @return string
     */
    public static function getOrdersSlug()
    {

        return apply_filters(
            'ofo_get_orders_slug',
            self::getSetting('orders_slug')
        );
    }

    /**
     * Get a single setting value
     *
     * @param string $key The setting key to retrieve
     * @return mixed The setting value or default
     */
    public static function getSetting($key)
    {

        $settings = self::getAllSettings();

        return $settings[$key] ?? self::getDefaultSettings()[$key] ?? null;
    }

    /**
     * Get all settings
     *
     * @return array
     */
    public static function getAllSettings()
    {

        $settings = get_option(self::$settingsKey, []);

        return wp_parse_args($settings, self::getDefaultSettings());
    }

    /**
     * Update a single setting
     *
     * @param string $key The setting key
     * @param mixed $value The setting value
     * @return boolean True if updated, false otherwise
     * @throws \InvalidArgumentException If setting key is not valid
     */
    public static function updateSetting($key, $value)
    {

        if (!array_key_exists($key, self::getDefaultSettings())) {

            throw new \InvalidArgumentException(
                sprintf(
                    'Invalid setting key: %s. Allowed keys are: %s',
                    esc_attr($key),
                    implode(', ', array_map('esc_html', array_keys(self::getDefaultSettings())))
                )
            );
        }

        $settings = self::getAllSettings();

        $settings[$key] = $value;

        return self::updateAllSettings($settings);
    }

    /**
     * Update multiple settings at once
     *
     * @param array $settings Array of settings to update
     * @return boolean True if updated, false otherwise
     * @throws \InvalidArgumentException If any setting key is not valid
     */
    public static function updateAllSettings($settings)
    {

        foreach ($settings as $key => $value) {

            if (!array_key_exists($key, self::getDefaultSettings())) {

                throw new \InvalidArgumentException(
                    sprintf(
                        'Invalid setting key: %s. Allowed keys are: %s',
                        esc_attr($key),
                        implode(', ', array_map('esc_html', array_keys(self::getDefaultSettings())))
                    )
                );
            }
        }

        $settings = self::sanitizeSettings($settings);

        $currentSettings = self::getAllSettings();

        if (empty(vajofoArrayRecursiveDiff($settings, $currentSettings))) {

            return new WP_REST_Response([
                'status'   => 'warning',
                'message' => esc_html__('Settings are up to date', 'olena-food-ordering')
            ], 200);
        }

        $updated = update_option(self::$settingsKey, $settings);

        if (!$updated) {
            return false;
        }

        return true;
    }

    /**
     * Sanitize settings before saving
     *
     * @param array $settings The settings to sanitize
     * @return array Sanitized settings
     */
    protected static function sanitizeSettings($settings)
    {

        if (!is_array($settings)) {

            return [];
        }

        $sanitized = [];

        foreach ($settings as $key => $value) {

            $key = sanitize_key($key);

            // Handle array values differently
            if (is_array($value)) {
                $sanitized[$key] = apply_filters('ofo_sanitize_setting', wp_json_encode($value), $key, $value);
            } else {
                $sanitized[$key] = apply_filters('ofo_sanitize_setting', sanitize_title($value), $key, $value);
            }
        }

        return $sanitized;
    }

    /**
     * Validate if all required settings are set
     *
     * @return boolean|array True if all settings are valid, array of missing settings otherwise
     */
    public static function validateSettings()
    {

        $settings = self::getAllSettings();

        $missing = [];

        foreach (array_keys(self::getDefaultSettings()) as $requiredSetting) {

            if (empty($settings[$requiredSetting])) {

                $missing[] = $requiredSetting;
            }
        }

        return empty($missing) ? true : $missing;
    }

    /**
     * Initialize default settings if they don't exist
     *
     * @return boolean True if initialized, false otherwise
     */
    public static function initializeSettings()
    {

        if (false === get_option(self::$settingsKey)) {

            return add_option(self::$settingsKey, self::getDefaultSettings());
        }

        return false;
    }

    /**
     * Get Checkbox Setting
     * 
     * @return array
     */
    public static function getCheckboxSetting()
    {
        return apply_filters(
            'ofo_get_checkbox_setting',
            wp_json_encode(self::getSetting('checkbox_setting'))
        );
    }
}
