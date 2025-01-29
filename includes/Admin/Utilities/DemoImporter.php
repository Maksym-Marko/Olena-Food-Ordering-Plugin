<?php

namespace VAJOFOWPPGNext\Admin\Utilities;

use VAJOFOWPPGNext\Shared\SettingsManager;
use VAJOFOWPPGNext\Shared\AddOnsManager;
use VAJOFOWPPGNext\Shared\MenuManager;
use VAJOFOWPPGNext\Admin\Utilities\ImageUploader;
use VAJOFOWPPGNext\Shared\SettingsSanitizer;

class DemoImporter
{

    const IMPORT_PROGRESS_META_KEY = '_ofo_demo_import_progress';

    const VALID_STEPS = ['step-1', 'step-2', 'step-3', 'step-4', 'step-5', 'step-6'];

    /**
     * Base import directory path
     * 
     * @var string
     */
    protected static $importBasePath = VAJOFO_PLUGIN_ABS_PATH . 'includes/Activate/seeder/menu-import/';

    /**
     * Get the full path for an import file
     *
     * @param string $filename The filename to get path for
     * @return string The full file path
     */
    protected static function getImportFilePath(string $filename): string
    {

        return apply_filters(
            'ofo_get_import_file_path',
            static::$importBasePath . $filename,
            $filename
        );
    }

    /**
     * Load and validate import data from a file
     *
     * @param string $filename The file to load data from
     * @param string $dataType The type of data being loaded (for error messages)
     * @return array The loaded and validated data
     * @throws \Exception If file is missing or data is invalid
     */
    protected static function loadImportData(string $filename, string $dataType): array
    {

        $filePath = apply_filters('ofo_import_file_path', static::getImportFilePath($filename), $filename);

        if (!file_exists($filePath)) {
            throw new \Exception(sprintf('%s file not found at: %s', esc_html($dataType), esc_html($filePath)));
        }

        $data = include $filePath;

        if (!is_iterable($data)) {
            throw new \Exception(sprintf(
                'Invalid %s data: expected iterable, got %s',
                esc_html($dataType),
                esc_html(gettype($data))
            ));
        }

        return $data;
    }

    /**
     * Imports add-on categories
     * @throws Exception If file is missing, data is invalid, or category insertion fails
     * @return bool True on success
     */
    public static function importAddOnCategories()
    {

        $addOnCategoriesFile = static::loadImportData(
            'add-on-categories.php',
            'Add-on categories'
        );

        $addOnCategories = apply_filters('ofo_addon_categories_import_data', $addOnCategoriesFile);

        foreach ($addOnCategories as $index => $category) {
            // Validate required fields
            $requiredFields = ['name', 'description', 'slug'];
            foreach ($requiredFields as $field) {
                if (!isset($category[$field])) {
                    throw new \Exception(
                        sprintf("Missing required field %s in category at index %s",
                            esc_html($field),
                            esc_html($index)
                        )                        
                    );
                }
            }

            $term_args = array(
                'description' => $category['description'],
                'slug'        => $category['slug']
            );

            $result = wp_insert_term(
                esc_html($category['name']),
                SettingsManager::TAXONOMY_ADD_ON_TYPE_SLUG,
                $term_args
            );

            if (is_wp_error($result)) {
                throw new \Exception('Failed to insert category: ' . esc_html($result->get_error_message()));
            }
        }

        do_action('ofo_import_demo_addon_categories_success', $addOnCategories);

        return true;
    }

    /**
     * Imports add-ons and associates them with categories
     * @throws Exception If files are missing, data is invalid, or import operations fail
     * @return bool True on success
     */
    public static function importAddOns()
    {

        // Add-ons file
        $addOnsFile = static::loadImportData(
            'add-ons.php',
            'Add-ons'
        );

        $addOns = apply_filters('ofo_add_ons_import_data', $addOnsFile);

        // Categories file
        $categoriesFile = static::loadImportData(
            'add-on-categories.php',
            'Add-on categories'
        );

        $addOnCategories = apply_filters('ofo_addon_categories_import_data', $categoriesFile);

        // import
        foreach ($addOns as $index => $addon) {
            // Validate required fields
            $requiredFields = ['title', 'description', 'price', 'category_id'];
            foreach ($requiredFields as $field) {
                if (!isset($addon[$field])) {
                    throw new \Exception(
                        sprintf(
                            "Missing required field %s in add-on at index %s",
                            esc_html($field),
                            esc_html($index)
                        )                        
                    );
                }
            }

            $post_data = array(
                'post_title'    => !empty($addon['title']) ? sanitize_text_field($addon['title']) : '',
                'post_content'  => !empty($addon['description']) ? wp_kses_post($addon['description']) : '',
                'post_status'   => 'publish',
                'post_type'     => SettingsManager::ADD_ONS_SLUG
            );

            $post_id = wp_insert_post($post_data, true);
            if (is_wp_error($post_id)) {
                throw new \Exception(sprintf(
                    'Failed to create add-on post for "%s": %s',
                    esc_html($addon['title']),
                    esc_html($post_id->get_error_message())
                ));
            }

            $meta_update = update_post_meta($post_id, AddOnsManager::ADD_ON_PRICE_META_KEY, floatval($addon['price']));
            if (false === $meta_update) {
                wp_delete_post($post_id, true);
                throw new \Exception(sprintf(
                    'Failed to update price meta for add-on "%s" (ID: %d)',
                    esc_html($addon['title']),
                    esc_html($post_id)
                ));
            }

            $category = array_filter($addOnCategories, function ($cat) use ($addon) {
                return $cat['id'] === $addon['category_id'];
            });

            $category = reset($category);
            if (!$category) {
                wp_delete_post($post_id, true);
                throw new \Exception(sprintf(
                    'Category ID %d not found for add-on "%s"',
                    esc_html($addon['category_id']),
                    esc_html($addon['title'])
                ));
            }

            $term_result = wp_set_object_terms($post_id, $category['slug'], SettingsManager::TAXONOMY_ADD_ON_TYPE_SLUG);
            if (is_wp_error($term_result)) {
                wp_delete_post($post_id, true);
                throw new \Exception(sprintf(
                    'Failed to set category for add-on "%s" (ID: %d): %s',
                    esc_html($addon['title']),
                    intval($post_id),
                    esc_html($term_result->get_error_message())
                ));
            }
        }

        do_action('ofo_import_demo_add_ons_success', $addOns, $addOnCategories);

        return true;
    }

    /**
     * Imports menu categories
     * @throws Exception If file is missing, data is invalid, or category insertion fails
     * @return bool True on success
     */
    public static function importMenuCategories()
    {

        // Menu categories file
        $menuCategoriesFile = static::loadImportData(
            'menu-categories.php',
            'Menu categories'
        );

        $menuCategories = apply_filters('ofo_menu_categories_import_data', $menuCategoriesFile);

        // import
        foreach ($menuCategories as $index => $category) {
            // Validate required fields
            $requiredFields = ['name', 'description', 'slug'];
            foreach ($requiredFields as $field) {
                if (!isset($category[$field])) {
                    throw new \Exception(
                        sprintf(
                            "Missing required field %s in menu category at index %s",
                            esc_html($field),
                            esc_html($index)
                        )
                    );
                }
            }

            $term_args = array(
                'description' => wp_kses_post($category['description']),
                'slug'        => sanitize_title($category['slug'])
            );

            $result = wp_insert_term(
                wp_strip_all_tags($category['name']),
                SettingsManager::TAXONOMY_MENU_TYPE_SLUG,
                $term_args
            );

            if (is_wp_error($result)) {
                throw new \Exception(sprintf(
                    'Failed to insert menu category "%s": %s',
                    esc_html($category['name']),
                    esc_html($result->get_error_message())
                ));
            }
        }

        do_action('ofo_import_demo_menu_categories_success', $menuCategories);

        return true;
    }

    /**
     * Imports menu tags
     * @throws Exception If file is missing, data is invalid, or tag insertion fails
     * @return bool True on success
     */
    public static function importMenuTags()
    {

        $menuTagsFile = static::loadImportData(
            'menu-tags.php',
            'Menu tags'
        );

        $menuTags = apply_filters('ofo_menu_tags_import_data', $menuTagsFile);

        // import
        foreach ($menuTags as $index => $tag) {
            // Validate required fields
            $requiredFields = ['name', 'description', 'slug'];
            foreach ($requiredFields as $field) {
                if (!isset($tag[$field])) {
                    throw new \Exception(
                        sprintf(
                            "Missing required field %s in menu tag at index %s",
                            esc_html($field),
                            esc_html($index)
                        )
                    );
                }
            }

            $term_args = array(
                'description' => wp_kses_post($tag['description']),
                'slug'        => sanitize_title($tag['slug'])
            );

            $result = wp_insert_term(
                wp_strip_all_tags($tag['name']),
                SettingsManager::TAXONOMY_MENU_TAG_SLUG,
                $term_args
            );

            if (is_wp_error($result)) {
                throw new \Exception(sprintf(
                    'Failed to insert menu tag "%s": %s',
                    esc_html($tag['name']),
                    esc_html($result->get_error_message())
                ));
            }
        }

        do_action('ofo_import_demo_menu_tags_success', $menuTags);

        return true;
    }

    /**
     * Imports menu items with their categories, tags, and add-ons
     * @throws Exception If files are missing, data is invalid, or import operations fail
     * @return bool True on success
     */
    public static function importMenuItems()
    {

        // Menu items file
        $menuItemsFile = static::loadImportData(
            'menu-items.php',
            'Menu items'
        );

        $menuItems = apply_filters('ofo_menu_items_import_data', $menuItemsFile);

        // Menu categories file
        $menuCategoriesFile = static::loadImportData(
            'menu-categories.php',
            'Menu categories'
        );

        $menuCategories = apply_filters('ofo_menu_categories_import_data', $menuCategoriesFile);

        // Menu tags file
        $menuTagsFile = static::loadImportData(
            'menu-tags.php',
            'Menu tags'
        );

        $menuTags = apply_filters('ofo_menu_tags_import_data', $menuTagsFile);

        // Add-ons file
        $addOnsFile = static::loadImportData(
            'add-ons.php',
            'Add-ons'
        );

        $addOns = apply_filters('ofo_add_ons_import_data', $addOnsFile);

        // Add-on categories file
        $addOnCategoriesFile = static::loadImportData(
            'add-on-categories.php',
            'Add-on categories'
        );

        $addOnCategories = apply_filters('ofo_addon_categories_import_data', $addOnCategoriesFile);

        // import
        foreach ($menuItems as $index => $item) {

            // Validate required fields
            $requiredFields = ['title', 'description', 'price', 'menu_category_id'];

            foreach ($requiredFields as $field) {
                if (!isset($item[$field])) {
                    throw new \Exception(
                        sprintf(
                            "Missing required field %s in menu item at index %s",
                            esc_html($field),
                            esc_html($index)
                        )
                    );
                }
            }

            $post_data = array(
                'post_title'    => !empty($item['title']) ? sanitize_text_field($item['title']) : '',
                'post_content'  => !empty($item['description']) ? wp_kses_post($item['description']) : '',
                'post_excerpt'  => !empty($item['excerpt']) ? wp_kses_post($item['excerpt']) : '',
                'post_status'   => 'publish',
                'post_type'     => SettingsManager::MENU_SLUG
            );

            $post_id = wp_insert_post($post_data);

            if (is_wp_error($post_id)) {
                throw new \Exception(sprintf(
                    'Failed to create menu item "%s": %s',
                    esc_html($item['title']),
                    esc_html($post_id->get_error_message())
                ));
            }

            // Upload menu item thumbnail
            if (isset($item['thumbnail']) && file_exists($item['thumbnail'])) {

                try {

                    $imageUploader = new ImageUploader($item['thumbnail'], $post_id);
                    $attachmentId = $imageUploader->uploadToMediaLibrary();

                    // Only try to set featured image if upload was successful
                    if (!is_wp_error($attachmentId)) {

                        $imageUploader->setAsFeaturedImage($attachmentId);
                    }
                } catch (\Exception $e) {

                    // Silently continue if image upload fails
                }
            }

            // Set price
            $price_update = update_post_meta($post_id, MenuManager::MENU_ITEM_PRICE_KEY, $item['price']);

            if (false === $price_update) {
                wp_delete_post($post_id, true);
                throw new \Exception(sprintf(
                    'Failed to set price for menu item "%s"',
                    esc_html($item['title'])
                ));
            }

            // Add menu category
            $category = array_filter($menuCategories, function ($cat) use ($item) {
                return $cat['id'] === $item['menu_category_id'];
            });

            $category = reset($category);

            if ($category) {

                $category_result = wp_set_object_terms($post_id, $category['slug'], SettingsManager::TAXONOMY_MENU_TYPE_SLUG);
                if (is_wp_error($category_result)) {
                    wp_delete_post($post_id, true);
                    throw new \Exception(sprintf(
                        'Failed to set category for menu item "%s": %s',
                        esc_html($item['title']),
                        esc_html($category_result->get_error_message())
                    ));
                }
            } else {

                wp_delete_post($post_id, true);
                throw new \Exception(sprintf(
                    'Category ID %d not found for menu item "%s"',
                    esc_html($item['menu_category_id']),
                    esc_html($item['title'])
                ));
            }

            // Add tags
            if (!empty($item['tags'])) {
                $tag_slugs = [];

                foreach ($item['tags'] as $tag_id) {
                    $tag = array_filter($menuTags, function ($t) use ($tag_id) {
                        return $t['id'] === $tag_id;
                    });
                    $tag = reset($tag);

                    if ($tag) {
                        $tag_slugs[] = $tag['slug'];
                    } else {
                        wp_delete_post($post_id, true);
                        throw new \Exception(sprintf(
                            'Tag ID %d not found for menu item "%s"',
                            esc_html($tag_id),
                            esc_html($item['title'])
                        ));
                    }
                }

                if (!empty($tag_slugs)) {
                    $tags_result = wp_set_object_terms($post_id, $tag_slugs, SettingsManager::TAXONOMY_MENU_TAG_SLUG);
                    if (is_wp_error($tags_result)) {
                        wp_delete_post($post_id, true);
                        throw new \Exception(sprintf(
                            'Failed to set tags for menu item "%s": %s',
                            esc_html($item['title']),
                            esc_html($tags_result->get_error_message())
                        ));
                    }
                }
            }

            // Add available add-ons
            if (!empty($item['available_add_ons'])) {

                $_addOns = [];

                foreach ($item['available_add_ons'] as $addonId => $addOnData) {

                    // Find the add-on from our data
                    $addon = array_filter($addOns, function ($a) use ($addonId) {
                        return $a['id'] === $addonId;
                    });
                    $addon = reset($addon);

                    if (!$addon) {
                        wp_delete_post($post_id, true);
                        throw new \Exception(sprintf(
                            'Add-on ID %d not found for menu item "%s"',
                            esc_html($addonId),
                            esc_html($item['title'])
                        ));
                    }

                    $addonCategory = array_filter($addOnCategories, function ($cat) use ($addon) {
                        return $cat['id'] === $addon['category_id'];
                    });
                    $addonCategory = reset($addonCategory);

                    if (!$addonCategory) {
                        wp_delete_post($post_id, true);
                        throw new \Exception(sprintf(
                            'Add-on category not found for add-on "%s" in menu item "%s"',
                            esc_html($addon['title']),
                            esc_html($item['title'])
                        ));
                    }

                    // Get the term object for this category
                    $term = get_term_by('slug', $addonCategory['slug'], SettingsManager::TAXONOMY_ADD_ON_TYPE_SLUG);

                    if (!$term) {
                        wp_delete_post($post_id, true);
                        throw new \Exception(sprintf(
                            'Term not found for add-on category "%s" in menu item "%s"',
                            esc_html($addonCategory['slug']),
                            esc_html($item['title'])
                        ));
                    }

                    // Find the add-on post by title
                    $addonPost = get_posts([
                        'post_type' => SettingsManager::ADD_ONS_SLUG,
                        'title' => $addon['title'],
                        'posts_per_page' => 1,
                        'post_status' => 'publish'
                    ]);

                    if (empty($addonPost)) {

                        wp_delete_post($post_id, true);

                        throw new \Exception(sprintf(
                            'Add-on post "%s" not found for menu item "%s"',
                            esc_html($addon['title']),
                            esc_html($item['title'])
                        ));
                    }

                    // Initialize the category array if it doesn't exist
                    if (!isset($_addOns[$term->term_id])) {
                        $_addOns[$term->term_id] = [];
                    }

                    // Add the add-on post ID to its category array
                    $_addOns[$term->term_id][$addonPost[0]->ID] = $addOnData;
                }

                if (!empty($_addOns)) {

                    $addons_update = update_post_meta($post_id, MenuManager::MENU_ITEM_AVAILABLE_ADD_ONS_KEY, $_addOns);
                    if (false === $addons_update) {
                        wp_delete_post($post_id, true);
                        throw new \Exception(sprintf(
                            'Failed to save add-ons data for menu item "%s"',
                            esc_html($item['title'])
                        ));
                    }
                }
            }
        }

        do_action('ofo_import_demo_menu_items_success', $menuItems, $menuCategories, $menuTags, $addOns, $addOnCategories);

        return true;
    }

    /**
     * Creates a "Menu" page with the food ordering shortcode if it doesn't exist.
     * 
     * This method checks for an existing page with the title "Menu". If the page
     * doesn't exist, it creates a new published page with the [olena_food_ordering_store]
     * shortcode as its content. If the page already exists, it returns its ID.
     * 
     * @return int The ID of the created or existing Menu page
     * @since 1.0.0
     */
    public static function createMenuPage()
    {

        $content = '[olena_food_ordering_store]';
        if (function_exists('wp_is_block_theme') && wp_is_block_theme()) {

            $content = '<!-- wp:shortcode -->[olena_food_ordering_store]<!-- /wp:shortcode -->';
        }

        $pageData = array(
            'post_title'    => __('Menu', 'olena-food-ordering'),
            'post_content'  => $content,
            'post_status'   => 'publish',
            'post_author'   => get_current_user_id(),
            'post_type'     => 'page'
        );

        $pageData = apply_filters('ofo_create_menu_page_data', $pageData);

        // Insert the page into the database
        $pageId = wp_insert_post($pageData);

        if (!is_wp_error($pageId)) {

            SettingsSanitizer::sanitize_store_url();

            SettingsSanitizer::sanitize_free_delivery_requirements();

            SettingsManager::updateSetting('store_url', get_permalink($pageId));

            do_action('ofo_create_menu_page_success', $pageId);

            return $pageId;
        }

        return $pageId;
    }
}
