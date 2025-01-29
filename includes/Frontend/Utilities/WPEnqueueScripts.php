<?php

/**
 * The WPEnqueueScripts class.
 *
 * This class is used to register 
 * styles and scripts for the frontend area.
 */

namespace VAJOFOWPPGNext\Frontend\Utilities;

use VAJOFOWPPGNext\Shared\SettingsManager;

class WPEnqueueScripts
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

        add_action('wp_enqueue_scripts', [$this, 'mainMenu']);

        add_action('wp_enqueue_scripts', [$this, 'singleItemButton']);

        add_action('wp_enqueue_scripts', [$this, 'cartWidget']);

        add_action('wp_enqueue_scripts', [$this, 'dequeueApiFetch'], 100);
    }

    /**
     * Prevents unnecessary API calls for non-logged-in users by preloading a null response
     * for the WordPress user endpoint.
     * 
     * This function adds inline JavaScript that creates a preloading middleware for the
     * wp.apiFetch utility. When a non-logged-in user accesses the site, instead of making
     * an actual API request to '/wp/v2/users/me', the middleware returns a preloaded
     * null response with a 401 status code. This prevents unnecessary HTTP requests and
     * eliminates console errors related to unauthorized user data access.
     * 
     * The function only runs for non-logged-in users to maintain normal functionality
     * for authenticated users.
     * 
     * @return void
     */
    public function dequeueApiFetch(): void
    {
        if (!is_user_logged_in()) {

            wp_add_inline_script(
                'wp-api-fetch',
                sprintf(
                    'wp.apiFetch.use( wp.apiFetch.createPreloadingMiddleware( %s ) );',
                    wp_json_encode([
                        '/wp/v2/users/me?context=edit' => [
                            'body' => null,
                            'headers' => ['status' => 401]
                        ]
                    ])
                ),
                'after'
            );
        }
    }

    /**
     * Enqueue styles and scripts for 
     * main menu page
     * 
     * @return void
     */
    public function mainMenu(): void
    {

        // dependencies
        wp_enqueue_script(
            "{$this->uniqueString}-dependencies",
            $this->pluginUrl . 'build/dependencies/vendors/index.js',
            ['react'],
            $this->version,
            true
        );

        $mainMenuHandler = "{$this->uniqueString}-store-scripts";
        wp_enqueue_script(
            $mainMenuHandler,
            $this->pluginUrl . 'build/frontend/olena-store/index.js',
            ["{$this->uniqueString}-dependencies"],
            $this->version,
            true
        );

        // Localizer
        wp_localize_script(
            $mainMenuHandler,
            "{$this->uniqueString}MainMenuLocalizer",
            [
                'ajaxURL'   => admin_url('admin-ajax.php'),
                'permalinkStructure' => get_option('permalink_structure'),
                'permalinkPage' => admin_url('options-permalink.php'),
                'nonce' => wp_create_nonce('wp_rest'),
                'defaultImage' => VAJOFO_PLUGIN_URL . 'assets/images/default.jpg',
                'editMenuItemBaseUrl' => current_user_can('administrator') ? admin_url('post.php') : null
            ]
        );

        // Main Menu style
        wp_enqueue_style(
            "{$this->uniqueString}-store-style",
            $this->pluginUrl . 'build/frontend/olena-store/index.css',
            [],
            $this->version
        );
    }

    /**
     * Enqueue styles and scripts for 
     * single item button
     * 
     * @return void
     */
    public function singleItemButton(): void
    {

        // dependencies
        wp_enqueue_script(
            "{$this->uniqueString}-dependencies",
            $this->pluginUrl . 'build/dependencies/vendors/index.js',
            ['react'],
            $this->version,
            true
        );

        $singleItemButtonHandler = "{$this->uniqueString}-single-item-button-scripts";
        wp_enqueue_script(
            $singleItemButtonHandler,
            $this->pluginUrl . 'build/frontend/single-item-button/index.js',
            ["{$this->uniqueString}-dependencies"],
            $this->version,
            true
        );

        // Localizer
        wp_localize_script(
            $singleItemButtonHandler,
            "{$this->uniqueString}SingleItemButtonLocalizer",
            [
                'ajaxURL'   => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('wp_rest'),
                'postId' => get_the_ID(),
                'storeUrl' => SettingsManager::getSetting('store_url'),
            ]
        );

        // Single Item Button style
        wp_enqueue_style(
            "{$this->uniqueString}-single-item-button-style",
            $this->pluginUrl . 'build/frontend/single-item-button/index.css',
            [],
            $this->version
        );
    }

    /**
     * Enqueue styles and scripts for 
     * cart widget
     * 
     * @return void
     */
    public function cartWidget(): void
    {

        // dependencies
        wp_enqueue_script(
            "{$this->uniqueString}-dependencies",
            $this->pluginUrl . 'build/dependencies/vendors/index.js',
            ['react'],
            $this->version,
            true
        );

        $cartWidgetHandler = "{$this->uniqueString}-cart-widget-scripts";
        wp_enqueue_script(
            $cartWidgetHandler,
            $this->pluginUrl . 'build/frontend/cart-widget/index.js',
            ["{$this->uniqueString}-dependencies"],
            $this->version,
            true
        );

        // Localizer
        wp_localize_script(
            $cartWidgetHandler,
            "{$this->uniqueString}CartWidgetLocalizer",
            [
                'storeUrl' => SettingsManager::getSetting('store_url'),
            ]
        );

        // Single Item Button style
        wp_enqueue_style(
            "{$this->uniqueString}-cart-widget-style",
            $this->pluginUrl . 'build/frontend/cart-widget/index.css',
            [],
            $this->version
        );
    }
}
