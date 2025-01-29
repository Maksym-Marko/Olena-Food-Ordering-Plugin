<?php

/**
 * Plugin Name:       Olena Food Ordering
 * Description:       Olena Food Ordering - a simple yet powerful WordPress plugin for restaurants and food trucks. Easily manage your menu, handle orders with custom add-ons, and organize pickups. Mobile-friendly interface ensures smooth ordering experience for your customers
 * Version:           1.1
 * Requires at least: 6.0
 * Requires PHP:      7.4.3
 * Author:            Maksym Marko
 * Author URI:        https://markomaksym.com.ua/
 * Text Domain:       olena-food-ordering
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Requires Plugins:
 */

defined('ABSPATH') || exit;

/**
 * Define VAJOFO_PLUGIN_VERSION
 * 
 * The version of all CSS and JavaScript files
 * in the plugin. Change for caching purpose.
 */
if (!defined('VAJOFO_PLUGIN_VERSION')) {

    define('VAJOFO_PLUGIN_VERSION', '1.1'); // '1.1'
}

/**
 * Define VAJOFO_PLUGIN_UNIQUE_STRING
 * 
 * Unique string - vajofo.
 * This string will be used to avoid plugin conflicts.
 */
if (!defined('VAJOFO_PLUGIN_UNIQUE_STRING')) {

    define('VAJOFO_PLUGIN_UNIQUE_STRING', 'vajofo');
}

/**
 * Define VAJOFO_PLUGIN_PATH
 * 
 * D:\xampp\htdocs\my-domain.com\wp-content\plugins\olena-food-ordering\olena-food-ordering.php
 */
if (!defined('VAJOFO_PLUGIN_PATH')) {

    define('VAJOFO_PLUGIN_PATH', __FILE__);
}

/**
 * Define VAJOFO_PLUGIN_URL
 * 
 * Return http://my-domain.com/wp-content/plugins/olena-food-ordering/
 */
if (!defined('VAJOFO_PLUGIN_URL')) {

    define('VAJOFO_PLUGIN_URL', plugins_url('/', __FILE__));
}

/**
 * Define VAJOFO_PLUGIN_BASE_NAME
 * 
 * Return olena-food-ordering/olena-food-ordering.php
 */
if (!defined('VAJOFO_PLUGIN_BASE_NAME')) {

    define('VAJOFO_PLUGIN_BASE_NAME', plugin_basename(__FILE__));
}

/**
 * Define VAJOFO_PLUGIN_ABS_PATH
 * 
 * D:\xampp\htdocs\my-domain.com\wp-content\plugins\olena-food-ordering/
 */
if (!defined('VAJOFO_PLUGIN_ABS_PATH')) {

    define('VAJOFO_PLUGIN_ABS_PATH', dirname(VAJOFO_PLUGIN_PATH) . '/');
}

/**
 * Run plugin if PHP >= 7.4.30
 */
if (PHP_VERSION_ID >= 70430) {

    /**
     * Autoload.
     */
    require VAJOFO_PLUGIN_ABS_PATH . 'vendor/autoload.php';

    /**
     * Helper functions.
     */
    require_once VAJOFO_PLUGIN_ABS_PATH . 'includes/Shared/functions.php';

    /**
     * activation|deactivation.
     */
    require_once VAJOFO_PLUGIN_ABS_PATH . 'install.php';

    /**
     * Run plugin parts.
     */
    require_once VAJOFO_PLUGIN_ABS_PATH . 'index.php';
}
