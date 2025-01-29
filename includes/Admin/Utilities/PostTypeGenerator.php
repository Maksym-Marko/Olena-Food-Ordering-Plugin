<?php

/**
 * The PostTypeGenerator class.
 *
 * Here you can register as many CPTs as you wish.
 */

namespace VAJOFOWPPGNext\Admin\Utilities;

use VAJOFOWPPGNext\Admin\Entities\PostType;

class PostTypeGenerator extends PostType
{

    /**
     * Get PostType instance and use it for 
     * CPT creation.
     * 
     * @param string $postType   The post type slug.
     * @param array  $labels     List of labels. 
     *                           The full list is
     *                           the parent class.
     * @param array  $properties List of properties. 
     * 
     * @return void
     */
    public static function create(string $postType, array $labels, array $properties): void
    {

        $instance = new static($postType);

        $instance->labels($labels);

        $instance->properties($properties);

        $instance->register();
    }

    /**
     * Olena Menu CPT
     * 
     * @return void
     */
    public static function registerMenuPostType($postType, $rewriteSlug): void
    {

        $labels = apply_filters('ofo_menu_post_type_labels', [
            'name'               => __('Menu', 'olena-food-ordering'),
            'singular_name'      => __('Menu', 'olena-food-ordering'),
            'add_new'            => __('Add a new one', 'olena-food-ordering'),
            'add_new_item'       => __('Add a Menu Item', 'olena-food-ordering'),
            'edit_item'          => __('Edit the Menu Item', 'olena-food-ordering'),
            'new_item'           => __('New Menu Item', 'olena-food-ordering'),
            'view_item'          => __('See the Menu Item', 'olena-food-ordering'),
            'search_items'       => __('Find a Menu Item', 'olena-food-ordering'),
            'not_found'          => __('Menu Items not found', 'olena-food-ordering'),
            'not_found_in_trash' => __('No Menu Items found in the trash', 'olena-food-ordering'),
            'menu_name'          => __('Menu Items', 'olena-food-ordering'),
        ]);

        $properties = apply_filters('ofo_menu_post_type_properties', [

            'rewrite'            => ['slug' => $rewriteSlug],
            'show_in_rest'       => false,

            'capability'         => 'manage_options',
            'menu_icon'          => 'data:image/svg+xml;base64,' . base64_encode('
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                </svg>
            ')
        ]);

        self::create(
            $postType,
            $labels,
            $properties
        );
    }

    /**
     * Olena Menu Add-ons CPT
     * 
     * @return void
     */
    public static function registerAddOnsPostType($postType, $rewriteSlug): void
    {

        $labels = apply_filters('ofo_add_ons_post_type_labels', [
            'name'               => __('Add-ons', 'olena-food-ordering'),
            'singular_name'      => __('Add-on', 'olena-food-ordering'),
            'add_new'            => __('Add a new one', 'olena-food-ordering'),
            'add_new_item'       => __('Add a new Add-on', 'olena-food-ordering'),
            'edit_item'          => __('Edit the Add-on', 'olena-food-ordering'),
            'new_item'           => __('New Add-on', 'olena-food-ordering'),
            'view_item'          => __('See the Add-on', 'olena-food-ordering'),
            'search_items'       => __('Find a Add-on', 'olena-food-ordering'),
            'not_found'          => __('Add-ons not found', 'olena-food-ordering'),
            'not_found_in_trash' => __('No Add-ons found in the trash', 'olena-food-ordering'),
            'menu_name'          => __('Add-ons', 'olena-food-ordering'),
        ]);

        $properties = apply_filters('ofo_add_ons_post_type_properties', [
            'rewrite'            => ['slug' => $rewriteSlug],
            'show_in_rest'       => true,
            'capability_type'    => 'post',
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'           => true,
            'show_in_menu'      => true,

            'capability'         => 'manage_options',
            'menu_icon'          => 'data:image/svg+xml;base64,' . base64_encode('
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#9CA2A7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
            ')
        ]);

        self::create(
            $postType,
            $labels,
            $properties
        );
    }

    /**
     * Olena Orders CPT
     * 
     * @param string $postType     The post type slug
     * @param string $rewriteSlug  The rewrite slug for URLs
     * @return void
     */
    public static function registerOrdersPostType($postType, $rewriteSlug): void
    {
        $labels = apply_filters('ofo_orders_post_type_labels', [
            'name'               => __('Orders', 'olena-food-ordering'),
            'singular_name'      => __('Order', 'olena-food-ordering'),
            'add_new'            => __('Add New', 'olena-food-ordering'),
            'add_new_item'       => __('Add New Order', 'olena-food-ordering'),
            'edit_item'          => __('Edit Order', 'olena-food-ordering'),
            'new_item'           => __('New Order', 'olena-food-ordering'),
            'view_item'          => __('View Order', 'olena-food-ordering'),
            'search_items'       => __('Search Orders', 'olena-food-ordering'),
            'not_found'          => __('No orders found', 'olena-food-ordering'),
            'not_found_in_trash' => __('No orders found in trash', 'olena-food-ordering'),
            'menu_name'          => __('Orders', 'olena-food-ordering'),
        ]);

        $properties = apply_filters('ofo_orders_post_type_properties', [
            'rewrite'            => ['slug' => $rewriteSlug],
            'show_in_rest'       => true,
            'capability_type'    => 'post',
            'supports'           => ['title'],
            'menu_position'      => 25,
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'           => true,
            'show_in_menu'      => true,
            'menu_icon'         => 'data:image/svg+xml;base64,' . base64_encode('
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 3h18v18H3z"/>
                    <path d="M3 9h18"/>
                    <path d="M3 15h18"/>
                    <path d="M9 3v18"/>
                    <path d="M15 3v18"/>
                </svg>
            ')
        ]);

        self::create(
            $postType,
            $labels,
            $properties
        );
    }
}
