<?php

namespace VAJOFOWPPGNext\Shared;

defined('ABSPATH') || exit;

use VAJOFOWPPGNext\Shared\SettingsManager;
use VAJOFOWPPGNext\Shared\Exceptions\AddOnsNoChangesException;
use VAJOFOWPPGNext\Shared\MenuManager;
use WP_REST_Response;

/**
 * Manages WordPress add-ons organization and retrieval.
 * 
 * This class handles the organization and fetching of add-ons based on their taxonomies
 * in a WordPress environment. It provides methods to retrieve add-ons grouped by their
 * respective term categories.
 */
class AddOnsManager
{
    /** @var string Custom post type slug for add-ons */
    private $addOnsPostType;

    /** @var string Taxonomy slug for add-on types */
    private $addOnsTaxonomy;

    /**
     * Meta key for storing add-on prices.
     *
     * This key is used to store and retrieve the price value
     * associated with individual add-ons.
     *
     * @var string
     * @access private
     */
    const ADD_ON_PRICE_META_KEY = 'olena-add-on-price';

    /**
     * Initializes the AddOnsManager with necessary slugs from SettingsManager.
     */
    public function __construct()
    {

        $this->addOnsPostType = SettingsManager::ADD_ONS_SLUG;
        $this->addOnsTaxonomy = SettingsManager::TAXONOMY_ADD_ON_TYPE_SLUG;
    }

    /**
     * Retrieves all published add-ons organized by their taxonomy terms.
     * 
     * @return array An associative array organized by term IDs, containing:
     *               - name: Term name
     *               - slug: Term slug
     *               - description: Term description
     *               - add_ons: Array of add-ons belonging to the term, where each add-on contains:
     *                         - name: Add-on title
     *                         - content: Add-on content
     */
    public function getOrganizedAddons(): array
    {
        $organized_addons = [];

        $terms = get_terms([
            'taxonomy' => $this->addOnsTaxonomy,
            'hide_empty' => false
        ]);

        if (empty($terms) || is_wp_error($terms)) {
            return $organized_addons;
        }

        foreach ($terms as $term) {
            $addon_items = $this->getAddonsByTerm($term->term_id);

            $organized_addons[$term->term_id] = [
                'name' => $term->name,
                'slug' => $term->slug,
                'description' => $term->description,
                'add_ons' => $addon_items
            ];
        }

        return apply_filters('ofo_get_organized_addons', $organized_addons);
    }

    /**
     * Retrieves all published add-ons for a specific taxonomy term.
     * 
     * @param int $term_id The ID of the taxonomy term
     * @return array Associative array of add-ons, keyed by post ID, containing:
     *               - name: Add-on title
     *               - content: Add-on content
     */
    private function getAddonsByTerm(int $term_id): array
    {
        $addons = get_posts([
            'post_type' => $this->addOnsPostType,
            'numberposts' => -1,
            'post_status' => 'publish',
            'tax_query' => [
                [
                    'taxonomy' => $this->addOnsTaxonomy,
                    'field' => 'term_id',
                    'terms' => $term_id
                ]
            ]
        ]);

        $addons = apply_filters('ofo_get_addons_by_term_query', $addons, $term_id);

        $addon_items = [];
        foreach ($addons as $addon) {
            $addon_items[$addon->ID] = [
                'name' => $addon->post_title,
                'content' => $addon->post_content,
                'price' => floatval(get_post_meta($addon->ID, self::ADD_ON_PRICE_META_KEY, true))
            ];
        }

        return apply_filters('ofo_get_addons_by_term', $addon_items, $term_id);
    }

    /**
     * Retrieves add-ons associated with a specific post.
     * 
     * @param int $postId Post ID to get add-ons for
     * @return mixed Array of add-ons or false if none found
     */
    public static function getAddOnsPerPost($postId)
    {

        return apply_filters('ofo_get_addons_per_post', get_post_meta($postId, MenuManager::MENU_ITEM_AVAILABLE_ADD_ONS_KEY, true), $postId);
    }

    /**
     * Updates add-ons for a specific post.
     * 
     * @param int   $postId Post ID to update
     * @param array $addOns Add-ons data to save
     * @return bool True if update successful
     * @throws AddOnsNoChangesException If new add-ons match existing ones
     */
    public static function updateAddOns($postId, $addOns)
    {

        $newAddOns = self::sanitizeAddOns($addOns);

        $savedAddOns = self::getAddOnsPerPost($postId);

        if (empty(vajofoAddOnsRecursiveDiff($newAddOns, $savedAddOns))) {

            return new WP_REST_Response([
                'status'   => 'warning',
                'message' => esc_html__('Add-ons are up to date', 'olena-food-ordering')
            ], 200);
        }

        $newAddOns = apply_filters('ofo_update_addons_per_post', $newAddOns, $postId);

        $updated = update_post_meta($postId, MenuManager::MENU_ITEM_AVAILABLE_ADD_ONS_KEY, $newAddOns);

        if (!$updated) {

            return false;
        }

        return true;
    }

    /**
     * Sanitizes add-ons array data.
     * 
     * @param array $addOns Add-ons data to sanitize
     * @return array Sanitized add-ons array
     */
    protected static function sanitizeAddOns($addOns)
    {

        if (!is_array($addOns)) {
            return [];
        }

        $sanitized = [];
        foreach ($addOns as $key => $value) {
            $key = sanitize_key($key);

            if (is_array($value)) {
                $sanitized[$key] = self::recursiveSanitize($value);
            } else {
                $sanitized[$key] = apply_filters('ofo_sanitize_addon_value', sanitize_text_field($value), $value, $key);
            }
        }

        return $sanitized;
    }

    /**
     * Recursively sanitizes nested array data.
     * 
     * @param array $array Nested array to sanitize
     * @return array Sanitized nested array
     */
    private static function recursiveSanitize($array)
    {

        $sanitized = [];
        foreach ($array as $key => $value) {
            $key = sanitize_key($key);

            if (is_array($value)) {
                $sanitized[$key] = self::recursiveSanitize($value);
            } else {
                $sanitized[$key] = apply_filters('ofo_sanitize_addon_value', sanitize_text_field($value), $value, $key);
            }
        }
        return $sanitized;
    }

    /**
     * Updates a custom taxonomy term for add-on types.
     *
     * This method updates both the name and slug of a specified add-on category term.
     * It performs validation checks and uses WordPress core functions to update the term.
     *
     * @since 1.0.0
     *
     * @param int    $termId   The ID of the term to update.
     * @param string $newName  The new name for the term.
     * @param string $newSlug  The new slug for the term.
     *
     * @return bool|WP_Error Returns true on success, false if term doesn't exist,
     *                       or WP_Error object on failure.
     */
    public static function updateAddOnsCategory($termId, $newName, $newSlug)
    {

        // Check if term_id is valid
        if (!term_exists(intval($termId), SettingsManager::TAXONOMY_ADD_ON_TYPE_SLUG)) {

            throw new \InvalidArgumentException(
                sprintf(
                    /* translators: %d: term ID */
                    esc_html__('Invalid term ID: ', 'olena-food-ordering') . '%d' . esc_html__('. Term does not exist in taxonomy ', 'olena-food-ordering') . '%s',
                    esc_html($termId),
                    esc_html(SettingsManager::TAXONOMY_ADD_ON_TYPE_SLUG)
                )
            );
        }

        // Prepare the arguments for updating
        $args = array(
            'name' => $newName,
            'slug' => $newSlug
        );

        // Update the term
        $result = wp_update_term($termId, SettingsManager::TAXONOMY_ADD_ON_TYPE_SLUG, $args);

        // Check for errors
        if (is_wp_error($result)) {

            return $result;
        }

        return true;
    }

    /**
     * Updates an add-on's name and price in WordPress.
     *
     * This function updates both the post title (name) and custom meta field (price)
     * for a specified add-on. It performs validation on all inputs before processing
     * the update.
     *
     * @since 1.0.0
     *
     * @param int    $addOnId  The post ID of the add-on to update.
     * @param string $newName  The new name for the add-on.
     * @param float  $newPrice The new price for the add-on.
     *
     * @return true|\WP_Error Returns true on successful update, WP_Error on failure.
     *                        Possible error codes:
     *                        - 'invalid_id': If add-on ID is not a positive number
     *                        - 'invalid_name': If name is empty or not a string
     *                        - 'invalid_price': If price is negative or not numeric
     *                        - 'meta_update_failed': If price meta update fails
     *                        - WordPress core errors from wp_update_post()
     *
     * @uses wp_update_post()     To update the add-on's title
     * @uses update_post_meta()   To update the add-on's price
     * @uses sanitize_text_field() To sanitize the new name
     *
     * @example
     * ```php
     * // Update an add-on
     * $result = updateAddOn(123, 'Premium Feature', 29.99);
     * if (is_wp_error($result)) {
     *     echo $result->get_error_message();
     * }
     * ```
     */
    public static function updateAddOn($addOnId, $newName, $newPrice)
    {

        // Validate inputs
        if (!is_numeric($addOnId) || $addOnId <= 0) {
            return new \WP_Error('invalid_id', 'Invalid add-on ID');
        }

        if (empty($newName) || !is_string($newName)) {
            return new \WP_Error('invalid_name', 'Name cannot be empty');
        }

        if (!is_numeric($newPrice) || $newPrice < 0) {
            return new \WP_Error('invalid_price', 'Price must be a non-negative number');
        }

        // Update post title
        $post_data = array(
            'ID' => $addOnId,
            'post_title' => sanitize_text_field($newName)
        );

        $update_result = wp_update_post($post_data, true);
        if (is_wp_error($update_result)) {
            return $update_result;
        }

        // Update price meta
        $meta_result = update_post_meta(
            $addOnId,
            self::ADD_ON_PRICE_META_KEY,
            floatval($newPrice)
        );

        if ($meta_result === false) {
            return new \WP_Error('meta_update_failed', 'Failed to update price');
        }

        return true;
    }

    /**
     * Creates a new add-on in WordPress with the specified details.
     *
     * This function creates a new custom post type entry for an add-on with the provided
     * information. It handles validation, slug generation, and sets up all necessary
     * post data, meta fields, and taxonomy terms.
     *
     * @since 1.0.0
     *
     * @param int    $categoryId  The taxonomy term ID for the add-on category.
     * @param string $name       The display name of the add-on.
     * @param string $slug       Optional. The URL slug for the add-on. If empty, will be generated from name.
     * @param float  $price      The price of the add-on.
     * @param string $description The description/content for the add-on.
     *
     * @return int|\WP_Error Returns the post ID of the created add-on on success, WP_Error on failure.
     *                       Possible error codes:
     *                       - 'invalid_name': If name is empty or not a string
     *                       - 'duplicate_slug': If an add-on with the same slug already exists
     *                       - 'invalid_price': If price is negative or not numeric
     *                       - WordPress core errors from wp_insert_post()
     *                       - WordPress core errors from wp_set_object_terms()
     *
     * @uses wp_insert_post()          To create the add-on post
     * @uses update_post_meta()        To set the add-on's price
     * @uses wp_set_object_terms()     To assign the add-on to a category
     * @uses get_page_by_path()        To check for duplicate slugs
     * @uses sanitize_text_field()     To sanitize the name
     * @uses sanitize_textarea_field() To sanitize the description
     * @uses sanitize_title()          To sanitize the slug
     *
     * @example
     * ```php
     * // Create a new add-on
     * $result = createAddOn(
     *     5,                    // Category ID
     *     'Premium Feature',    // Name
     *     'premium-feature',    // Slug (optional)
     *     29.99,               // Price
     *     'Feature description' // Description
     * );
     * if (is_wp_error($result)) {
     *     echo $result->get_error_message();
     * } else {
     *     echo "Add-on created with ID: " . $result;
     * }
     * ```
     */
    public static function createAddOn($categoryId, $name, $slug, $price, $description)
    {

        // Validate inputs
        if (empty($name) || !is_string($name)) {
            return new \WP_Error('invalid_name', 'Name cannot be empty');
        }

        if (empty($slug)) {
            $slug = sanitize_title($name);
        } else {
            $slug = sanitize_title($slug);
        }

        // Check if slug already exists
        $existing_post = get_page_by_path($slug, OBJECT, SettingsManager::ADD_ONS_SLUG);
        if ($existing_post instanceof \WP_Post) {
            return new \WP_Error('duplicate_slug', 'An add-on with this slug already exists');
        }

        if (!is_numeric($price) || $price < 0) {
            return new \WP_Error('invalid_price', 'Price must be a non-negative number');
        }

        // Prepare post data
        $postData = array(
            'post_title'   => sanitize_text_field($name),
            'post_content' => sanitize_textarea_field($description),
            'post_status'  => 'publish',
            'post_type'    => SettingsManager::ADD_ONS_SLUG,
            'post_name'    => $slug
        );

        $postData = apply_filters('ofo_create_add_on_post_data', $postData, $categoryId, $name, $slug, $price, $description);

        // Insert the post
        $postId = wp_insert_post($postData, true);

        // Check for errors during post creation
        if (is_wp_error($postId)) {
            return $postId;
        }

        // Add price as post meta
        update_post_meta($postId, self::ADD_ON_PRICE_META_KEY, floatval($price));

        // Set the addon category taxonomy
        $result = wp_set_object_terms($postId, intval($categoryId), SettingsManager::TAXONOMY_ADD_ON_TYPE_SLUG);

        // Check for errors during taxonomy assignment
        if (is_wp_error($result)) {
            // If taxonomy assignment fails, delete the post we just created
            wp_delete_post($postId, true);
            return $result;
        }

        return $postId;
    }

    /**
     * Deletes an add-on from WordPress.
     * 
     * This function permanently removes an add-on and all its associated data,
     * including meta fields and taxonomy relationships. It performs validation
     * to ensure the add-on exists before attempting deletion.
     *
     * @since 1.0.0
     *
     * @param int $addOnId The post ID of the add-on to delete.
     *
     * @return true|\WP_Error Returns true on successful deletion, WP_Error on failure.
     *                        Possible error codes:
     *                        - 'invalid_id': If ID is empty or not numeric
     *                        - 'post_not_found': If add-on with given ID doesn't exist
     *                        - 'deletion_failed': If wp_delete_post operation fails
     *
     * @uses get_post()      To verify the add-on exists
     * @uses wp_delete_post() To remove the add-on and its data
     *
     * @example
     * ```php
     * // Delete an add-on
     * $result = deleteAddOn(123);
     * if (is_wp_error($result)) {
     *     echo $result->get_error_message();
     * } else {
     *     echo "Add-on successfully deleted";
     * }
     * ```
     */
    public static function deleteAddOn($addOnId)
    {

        if (empty($addOnId) || !is_numeric($addOnId)) {
            return new \WP_Error('invalid_id', 'ID cannot be empty');
        }

        // Check if post exists
        $post = get_post($addOnId);
        if (!$post) {
            return new \WP_Error('post_not_found', 'Add-on not found');
        }

        // Delete the post
        $deleted = apply_filters('ofo_delete_addon', wp_trash_post($addOnId), $addOnId);

        // Check if deletion was successful
        if ($deleted === false || is_null($deleted)) {
            return new \WP_Error('deletion_failed', 'Failed to delete the add-on');
        }

        // Return success
        return true;
    }

    /**
     * Creates a new add-on category taxonomy term.
     *
     * This function creates a new taxonomy term for categorizing add-ons. It handles
     * input validation, slug generation, and checks for existing categories to prevent
     * duplicates.
     *
     * @param string $name        The display name of the category.
     * @param string $slug        Optional. The URL slug for the category. If empty, will be generated from name.
     * @param string $description Optional. The description of the category.
     *
     * @return int|\WP_Error Returns the term ID of the created category on success, WP_Error on failure.
     *                       Possible error codes:
     *                       - 'invalid_name': If name is empty or not a string
     *                       - 'duplicate_slug': If a category with the same slug already exists
     *                       - WordPress core errors from wp_insert_term()
     *
     * @uses get_term_by()      To check for existing categories
     * @uses wp_insert_term()   To create the taxonomy term
     * @uses sanitize_title()   To sanitize the slug
     * @uses wp_strip_all_tags() To sanitize the name
     * @uses wp_kses_post()     To sanitize the description while allowing safe HTML
     *
     * @example
     * ```php
     * // Create a new add-on category
     * $result = createAddOnCategory(
     *     'Premium Features',     // Name
     *     'premium-features',     // Slug (optional)
     *     'High-end add-ons'     // Description (optional)
     * );
     * if (is_wp_error($result)) {
     *     echo $result->get_error_message();
     * } else {
     *     echo "Category created with ID: " . $result;
     * }
     * ```
     */
    public static function createAddOnCategory($name, $slug, $description)
    {

        // Validate inputs
        if (empty($name) || !is_string($name)) {
            return new \WP_Error('invalid_name', 'Name cannot be empty');
        }

        if (empty($slug)) {
            $slug = sanitize_title($name);
        } else {
            $slug = sanitize_title($slug);
        }

        // Check if taxonomy term already exists
        $existing_term = get_term_by('slug', $slug, SettingsManager::TAXONOMY_ADD_ON_TYPE_SLUG);
        if ($existing_term) {
            return new \WP_Error('duplicate_slug', 'An add-on category with this slug already exists');
        }

        // Prepare term arguments
        $args = array(
            'slug' => $slug,
            'description' => wp_kses_post($description)
        );

        $args = apply_filters('ofo_create_add_on_category_args', $args, $name, $slug, $description);

        // Insert the term
        $result = wp_insert_term(
            wp_strip_all_tags($name),
            SettingsManager::TAXONOMY_ADD_ON_TYPE_SLUG,
            $args
        );

        if (is_wp_error($result)) {
            return $result;
        }

        return $result['term_id'];
    }

    public static function deleteAddOnsCategoryAndAddOns($categoryId)
    {

        if (empty($categoryId) || !is_numeric($categoryId)) {
            return new \WP_Error('invalid_id', 'ID cannot be empty');
        }

        // Check if taxonomy exists
        $term = get_term($categoryId, SettingsManager::TAXONOMY_ADD_ON_TYPE_SLUG);
        if (is_wp_error($term) || !$term) {
            return new \WP_Error('term_not_found', 'Category does not exist');
        }

        // Get all posts (add-ons) associated with this category
        $associated_posts = get_posts([
            'post_type' => 'any',
            'tax_query' => [
                [
                    'taxonomy' => SettingsManager::TAXONOMY_ADD_ON_TYPE_SLUG,
                    'field'    => 'term_id',
                    'terms'    => $categoryId,
                ],
            ],
            'posts_per_page' => -1,
            'fields'         => 'ids',
        ]);

        $associated_posts = apply_filters('ofo_get_associated_posts', $associated_posts, $categoryId);

        // Move associated posts to trash
        foreach ($associated_posts as $post_id) {
            @wp_trash_post($post_id);
        }

        // Remove the term relationship from all trashed posts
        foreach ($associated_posts as $post_id) {
            wp_remove_object_terms($post_id, $categoryId, SettingsManager::TAXONOMY_ADD_ON_TYPE_SLUG);
        }

        // Delete the term and all its term meta
        $result = wp_delete_term($categoryId, SettingsManager::TAXONOMY_ADD_ON_TYPE_SLUG);

        if (is_wp_error($result)) {
            return $result;
        }

        if (!$result) {
            return new \WP_Error('deletion_failed', 'Failed to delete category');
        }

        return true;
    }
}
