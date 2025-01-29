<?php

/**
 * The TaxonomyGenerator class.
 *
 * Here you can register new taxonomies.
 */

namespace VAJOFOWPPGNext\Admin\Utilities;

use VAJOFOWPPGNext\Admin\Entities\Taxonomy;

class TaxonomyGenerator extends Taxonomy
{

    /**
     * Get Taxonomy instance and use it for 
     * new taxonomies creation.
     * 
     * @param string $taxonomy   Taxonomy slug.
     * @param array  $postTypes  List of registered post types.
     * @param array  $labels     List of labels. 
     * @param array  $properties List of properties. 
     * 
     * @return void
     */
    public static function create(string $taxonomy, array $postTypes, array $labels, array $properties): void
    {

        $instance = new static($taxonomy, $postTypes);

        $instance->labels($labels);

        $instance->properties($properties);

        $instance->register();
    }

    /**
     * Menu Type taxonomy
     * 
     * @return void
     */
    public static function registerMenuTypeTaxonomy($taxonomy, $rewriteSlug, $postTypes): void
    {

        $labels = [
            'name'    => __('Menu Types', 'olena-food-ordering'),
            'singular_name' => __('Menu Type', 'olena-food-ordering'),
            'menu_name' => __('Menu Types', 'olena-food-ordering'),
        ];

        /**
         * TODO: add setting to change slug via admin panel
         */
        $properties = [
            'rewrite' => ['slug' => $rewriteSlug],
        ];

        self::create(
            $taxonomy,
            $postTypes,
            $labels,
            $properties
        );
    }

    /**
     * Menu Tag taxonomy
     * 
     * @return void
     */
    public static function registerMenuTagTaxonomy($taxonomy, $rewriteSlug, $postTypes): void
    {

        $labels = [
            'name' => __('Menu Tags', 'olena-food-ordering'),
            'singular_name' => __('Menu Tag', 'olena-food-ordering'),
            'menu_name' => __('Menu Tags', 'olena-food-ordering'),
            'search_items' => __('Search Tags', 'olena-food-ordering'),
            'all_items' => __('All Tags', 'olena-food-ordering'),
            'edit_item' => __('Edit Tag', 'olena-food-ordering'),
            'update_item' => __('Update Tag', 'olena-food-ordering'),
            'add_new_item' => __('Add New Tag', 'olena-food-ordering'),
            'new_item_name' => __('New Tag Name', 'olena-food-ordering'),
        ];

        /**
         * TODO: add setting to change slug via admin panel
         */
        $properties = [
            'rewrite' => ['slug' => $rewriteSlug],
            'hierarchical' => false,
            'show_admin_column' => true,
            'show_in_rest' => true,
        ];

        self::create(
            $taxonomy,
            $postTypes,
            $labels,
            $properties
        );
    }

    /**
     * Add-on type taxonomy
     * 
     * @return void
     */
    public static function registerAddOnTypeTaxonomy($taxonomy, $rewriteSlug, $postTypes): void
    {

        $labels = [
            'name'    => __('Add-on Types', 'olena-food-ordering'),
            'singular_name' => __('Add-on Types', 'olena-food-ordering'),
            'menu_name' => __('Add-on Types', 'olena-food-ordering'),
        ];

        $properties = [
            'rewrite' => ['slug' => $rewriteSlug],
        ];

        self::create(
            $taxonomy,
            $postTypes,
            $labels,
            $properties
        );
    }
}
