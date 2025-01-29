<?php

namespace VAJOFOWPPGNext\Shared;

defined('ABSPATH') || exit;

use VAJOFOWPPGNext\Shared\SettingsManager;
use VAJOFOWPPGNext\Shared\AddOnsManager;

/**
 * Manages menu-related functionality and constants.
 *
 * This class provides centralized management of menu item meta keys and related
 * functionality for the food ordering system. It defines constants used throughout
 * the application for consistent meta key access.
 *
 * @package VAJOFOWPPGNext\Shared
 * @since 1.0.0
 */
class MenuManager
{

    /**
     * Meta key for storing menu item prices.
     * 
     * This constant defines the meta key used to store and retrieve
     * the price value for individual menu items.
     *
     * @var string
     */
    const MENU_ITEM_PRICE_KEY = 'olena-menu-item-price';

    /**
     * Meta key for storing add-ons manager data.
     *
     * This key is used to store and retrieve add-ons manager data.
     *
     * @var string
     * @access private
     */
    const MENU_ITEM_ADD_ONS_MANAGER_META_KEY = 'olena-menu-item-add-ons-manager';

    /**
     * Meta key for storing available add-ons.
     *
     * This key is used to store and retrieve the list of add-ons
     * associated with menu items or other post types.
     *
     * @var string
     */
    const MENU_ITEM_AVAILABLE_ADD_ONS_KEY = '_ofo_available_add_ons';

    protected $postType;
    protected $currentPage;
    protected $perPage;
    protected $order;
    protected $postId;

    /**
     * Initialize menu item manager with optional configuration.
     *
     * @param array $args {
     *     Optional. Array of arguments for configuring the menu item query.
     *
     *     @type string $postType    Post type to query. Default SettingsManager::MENU_SLUG.
     *     @type int    $currentPage Page number for pagination. Default 1.
     *     @type int    $perPage     Items per page. Default 10.
     *     @type string $order       Sort order ('ASC' or 'DESC'). Default 'ASC'.
     *     @type int    $postId      Post ID to query. Default 0.
     * }
     */
    public function __construct(array $args = [])
    {

        $this->postType = sanitize_text_field($args['postType'] ?? SettingsManager::MENU_SLUG);
        $this->currentPage = max(1, (int)($args['currentPage'] ?? 1));
        $this->perPage = max(1, (int)($args['perPage'] ?? 10));
        $this->postId = (int)($args['postId'] ?? 0);

        $allowedSortOrders = ['ASC', 'DESC'];
        $requestedSort = strtoupper(sanitize_text_field($args['order'] ?? 'ASC'));
        $this->order = in_array($requestedSort, $allowedSortOrders) ? $requestedSort : 'ASC';
    }

    /**
     * Retrieves a paginated list of menu items with their associated data.
     *
     * @return array[] Array containing:
     *                 'menuItems' - Array of menu items, each containing:
     *                              - id          (int)    Post ID
     *                              - title       (string) Post title
     *                              - description (string) Post content
     *                              - thumbnail   (string) Featured image URL
     *                              - price       (string) Menu item price
     *                              - add_ons     (array)  Associated add-ons
     *                              - categories  (array)  Associated categories
     *                              - tags        (array)  Associated tags
     *                 'available_addons' - Array of all available add-ons organized by type
     *                 'pagination'       - Array containing:
     *                                     - total_pages  (int) Total number of pages
     *                                     - current_page (int) Current page number
     *                                     - per_page     (int) Items per page
     *                                     - total_items  (int) Total number of items
     */
    public function getMenuItems()
    {

        $query = new \WP_Query([
            'post_type' => $this->postType,
            'posts_per_page' => $this->perPage,
            'paged' => $this->currentPage,
            'orderby' => 'title',
            'order' => $this->order
        ]);

        $menuItems = $query->posts;

        $totalPages = $query->max_num_pages;

        $addOnsManager = new AddOnsManager;

        $menuItems = [
            'menuItems' => array_map(function ($post) {
                return [
                    'id' => $post->ID,
                    'title' => $post->post_title,
                    'description' => $post->post_content,
                    'thumbnail' => get_the_post_thumbnail_url($post->ID, 'large'),
                    'price' => get_post_meta($post->ID, self::MENU_ITEM_PRICE_KEY, true),
                    'add_ons' => AddOnsManager::getAddOnsPerPost($post->ID),
                    'categories' => $this->getCategories($post->ID),
                    'tags' => $this->getTags($post->ID),
                ];
            }, $menuItems),
            'available_addons' => $addOnsManager->getOrganizedAddons(),
            'pagination' => [
                'total_pages' => $totalPages,
                'current_page' => $this->currentPage,
                'per_page' => $this->perPage,
                'total_items' => $query->found_posts
            ]
        ];

        return apply_filters('ofo_get_menu_items', $menuItems, $this->postType, $this->currentPage, $this->perPage, $this->order);
    }

    /**
     * Retrieves a single menu item by post ID.
     *
     * @return array|false Menu item data array or false on failure, containing:
     *                     - id          (int)    Post ID
     *                     - title       (string) Post title
     *                     - description (string) Post content
     *                     - thumbnail   (string) Featured image URL
     *                     - price       (string) Menu item price
     *                     - add_ons     (array)  Associated add-ons
     *                     - categories  (array)  Associated categories
     *                     - tags        (array)  Associated tags
     */
    public function getMenuItem()
    {
        $post = get_post($this->postId);

        if (!$post || $post->post_type !== $this->postType) {
            return false;
        }

        $menuItem = [
            'id' => $post->ID,
            'title' => $post->post_title,
            'description' => $post->post_content,
            'thumbnail' => get_the_post_thumbnail_url($post->ID, 'large'),
            'price' => get_post_meta($post->ID, self::MENU_ITEM_PRICE_KEY, true),
            'add_ons' => AddOnsManager::getAddOnsPerPost($post->ID),
            'categories' => $this->getCategories($post->ID),
            'tags' => $this->getTags($post->ID),
            'permalink' => get_permalink($post->ID)
        ];

        return apply_filters('ofo_get_menu_item', $menuItem, $this->postType, $this->postId);
    }

    /**
     * Retrieves categories associated with a specific post.
     *
     * @param int $postId The ID of the post to get categories for.
     * 
     * @return array[] Array of category data, each containing:
     *                 - id   (int)    Term ID
     *                 - name (string) Term name
     *                 - slug (string) Term slug
     *                 Additional fields may be added via the 'ofo_menu_item_get_category_data' filter.
     *
     * @filter ofo_menu_item_get_category_data Filters the data for each individual category.
     *         @param array    $categoryData The default category data
     *         @param WP_Term  $category     The category term object
     *         @param int      $postId       The post ID
     */
    protected function getCategories($postId)
    {

        $categories = [];

        $postCategories = wp_get_object_terms(
            $postId,
            SettingsManager::TAXONOMY_MENU_TYPE_SLUG,
            array('fields' => 'all')
        );

        if (!is_wp_error($postCategories)) {
            foreach ($postCategories as $category) {

                $categoryData = apply_filters(
                    'ofo_menu_item_get_category_data',
                    [
                        'id' => $category->term_id,
                        'name' => $category->name,
                        'slug' => $category->slug
                    ],
                    $category,
                    $postId
                );

                $categories[] = $categoryData;
            }
        }

        return $categories;
    }

    /**
     * Retrieves tags associated with a specific post.
     *
     * @param int $postId The ID of the post to get tags for.
     * 
     * @return array[] Array of tag data, each containing:
     *                 - id   (int)    Term ID
     *                 - name (string) Term name
     *                 - slug (string) Term slug
     *                 Additional fields may be added via the 'ofo_menu_item_get_tag_data' filter.
     *
     * @filter ofo_menu_item_get_tag_data Filters the data for each individual tag.
     *         @param array    $tagData The default tag data
     *         @param WP_Term  $tag     The tag term object
     *         @param int      $postId  The post ID
     */
    protected function getTags($postId)
    {

        $tags = [];

        $postTags = wp_get_object_terms(
            $postId,
            SettingsManager::TAXONOMY_MENU_TAG_SLUG,
            array('fields' => 'all')
        );

        if (!is_wp_error($postTags)) {
            foreach ($postTags as $tag) {

                $tagData = apply_filters(
                    'ofo_menu_item_get_tag_data',
                    [
                        'id' => $tag->term_id,
                        'name' => $tag->name,
                        'slug' => $tag->slug
                    ],
                    $tag,
                    $postId
                );

                $tags[] = $tagData;
            }
        }

        return $tags;
    }
}
