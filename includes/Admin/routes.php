<?php

/**
 * Here you can register new menu items.
 */

defined('ABSPATH') || exit;

use VAJOFOWPPGNext\Admin\Router;
use VAJOFOWPPGNext\Admin\Utilities\DemoImporter;

$router = new Router();

$mainMenuSlug = 'olena-settings';

/**
 * PAGE.
 * 
 * Add Main Menu Item.
 * 
 * /wp-admin/admin.php?page=olena-settings
 * */
$icon_svg = 'data:image/svg+xml;base64,' . base64_encode('
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
        <path fill="currentColor" d="M10 2C5.58 2 2 5.58 2 10s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 14c-3.31 0-6-2.69-6-6s2.69-6 6-6 6 2.69 6 6-2.69 6-6 6z"/>
        <path fill="currentColor" d="M10 6c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm0 6c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z"/>
    </svg>
    ');
$router->get('main')->properties([
    'menuSlug' => $mainMenuSlug,
    'pageTitle'  => esc_html__('Olena Food Ordering settings', 'olena-food-ordering'),
    'menuTitle'  => esc_html__('Olena Settings', 'olena-food-ordering'),
    'dashicons' => 'dashicons-admin-generic'
]);

/**
 * PAGE.
 * 
 * Add Sub Menu Item.
 * 
 * /wp-admin/admin.php?page=olena-settings#/import
 * */
if(empty(get_option(DemoImporter::IMPORT_PROGRESS_META_KEY, []))) {

    $router->get('main')
        ->menuAction('addSubmenuPage')
        ->properties([
            'parentSlug' => $mainMenuSlug,
            'pageTitle'  => esc_html__('Demo Import', 'olena-food-ordering'),
            'menuTitle'  => esc_html__('Demo Import', 'olena-food-ordering'),
            'menuSlug'   => "$mainMenuSlug#/import"
        ]);
}

/**
 * Add link to the Options page on the plugins page.
 * */
$router->get('main')
    ->menuAction('addOptionLink')
    ->properties([
        'menuTitle'   => esc_html__('Olena Settings', 'olena-food-ordering'),
        'menuSlug'    => $mainMenuSlug,
        'optionsPage' => 'admin.php',
    ]);

// Render all the pages.
$router->route();
