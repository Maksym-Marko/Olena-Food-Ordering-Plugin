<?php

/**
 * The AdminEnqueueScripts class.
 *
 * This class is used to register 
 * styles and scripts for admin area.
 */

namespace VAJOFOWPPGNext\Admin\Utilities;

use VAJOFOWPPGNext\Shared\SettingsManager;

class AdminEnqueueScripts
{

    /**
     * Unique string to avoid conflicts.
     * 
     * @var string
     */
    protected $uniqueString = VAJOFO_PLUGIN_UNIQUE_STRING;

    /**
     * The file version. Helps to cope with caching.
     * 
     * @var string
     */
    protected $version      = VAJOFO_PLUGIN_VERSION;

    /**
     * URL to the plugin folder.
     * 
     * @var string
     */
    protected $pluginUrl    = VAJOFO_PLUGIN_URL;

    /**
     * Enqueue all the scripts
     * 
     * @return void
     */
    public function enqueue(): void
    {

        // Settings page
        add_action('admin_enqueue_scripts', [$this, 'settingsScripts']);

        // Menu Item page
        add_action('admin_enqueue_scripts', [$this, 'addOnsManagerScripts']);

        // Order Details page
        add_action('admin_enqueue_scripts', [$this, 'orderDetailsScripts']);
    }

    /**
     * Enqueue styles and scripts for 
     * settings page
     * 
     * @return void
     */
    public function settingsScripts(): void
    {

        // dependencies
        $dependenciesHandler = "{$this->uniqueString}-dependencies";
        wp_enqueue_script(
            $dependenciesHandler,
            $this->pluginUrl . 'build/dependencies/vendors/index.js',
            ['react'],
            $this->version,
            true
        );

        /**
         * Settings page
        */
        wp_enqueue_style(
            "{$this->uniqueString}-settings-page-style",
            $this->pluginUrl . 'build/admin/settings-page/index.css',
            [],
            $this->version,
        );

        $settingsPage = "{$this->uniqueString}-settings-page";
        wp_enqueue_script(
            $settingsPage,
            $this->pluginUrl . 'build/admin/settings-page/index.js',
            [$dependenciesHandler, 'wp-api'],
            $this->version,
            true
        );

        wp_localize_script($settingsPage, 'wpApiSettings', array(
            'nonce' => wp_create_nonce('wp_rest'),
            'root'  => esc_url_raw(rest_url()),
            'permalinkStructure' => get_option('permalink_structure'),
            'permalinkPage' => admin_url('options-permalink.php')
        ));
    }

    /**
     * Enqueue styles and scripts for 
     * Edit Menu Item page
     * 
     * @return void
     */
    public function addOnsManagerScripts(): void
    {

        $screen = get_current_screen();

        if ($screen->post_type !== SettingsManager::MENU_SLUG || $screen->base !== 'post') return;

        // dependencies
        $dependenciesHandler = "{$this->uniqueString}-dependencies";
        wp_enqueue_script(
            $dependenciesHandler,
            $this->pluginUrl . 'build/dependencies/vendors/index.js',
            [],
            $this->version,
            true
        );

        wp_enqueue_style(
            "{$this->uniqueString}-add-ons-manager-style",
            $this->pluginUrl . 'build/admin/add-ons-manager/index.css',
            [],
            $this->version,
        );

        $settingsPage = "{$this->uniqueString}-add-ons-manager";
        wp_enqueue_script(
            $settingsPage,
            $this->pluginUrl . 'build/admin/add-ons-manager/index.js',
            [$dependenciesHandler, 'wp-api'],
            $this->version,
            true
        );

        wp_localize_script($settingsPage, 'wpApiAddOnsManager', array(
            'nonce' => wp_create_nonce('wp_rest'),
            'postId' => get_the_ID(),
            'permalinkStructure' => get_option('permalink_structure'),
            'permalinkPage' => admin_url('options-permalink.php')
        ));
    }

    /**
     * Enqueue styles and scripts for 
     * Order Details page
     * 
     * @return void
     */
    public function orderDetailsScripts(): void
    {

        $screen = get_current_screen();

        if ($screen->post_type !== SettingsManager::ORDERS_SLUG || $screen->base !== 'post') return;

        // dependencies
        $dependenciesHandler = "{$this->uniqueString}-dependencies";
        wp_enqueue_script(
            $dependenciesHandler,
            $this->pluginUrl . 'build/dependencies/vendors/index.js',
            [],
            $this->version,
            true
        );

        wp_enqueue_style(
            "{$this->uniqueString}-order-details-style",
            $this->pluginUrl . 'build/admin/order-details/index.css',
            [],
            $this->version,
        );

        $orderDetails = "{$this->uniqueString}-order-details";
        wp_enqueue_script(
            $orderDetails,
            $this->pluginUrl . 'build/admin/order-details/index.js',
            [$dependenciesHandler, 'wp-api'],
            $this->version,
            true
        );

        wp_localize_script($orderDetails, 'wpApiOrderDetails', array(
            'nonce' => wp_create_nonce('wp_rest'),
            'orderId' => get_the_ID(),
            'permalinkStructure' => get_option('permalink_structure'),
            'permalinkPage' => admin_url('options-permalink.php')
        ));
    }
}
