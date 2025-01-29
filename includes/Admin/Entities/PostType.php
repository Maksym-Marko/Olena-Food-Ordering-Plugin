<?php

/**
 * Class PostType.
 * This class is used to create CPT.
 * You can find examples here:
 * \includes\Admin\Utilities\PostTypeGenerator.php
 */

namespace VAJOFOWPPGNext\Admin\Entities;

class PostType
{

    /**
     * Unique string to avoid conflicts.
     * 
     * @var string
     */
    protected $uniqueString = 'vajofo';

    /**
     * Post type. Must not exceed 20 characters and 
     * may only contain lowercase alphanumeric characters, 
     * dashes, and underscores.
     * 
     * @var string
     */
    protected $postType;

    /**
     * Default labels. Will be override with user's labels.
     * 
     * @var array
     */
    protected $labels = [
        'name'               => 'Books',
        'singular_name'      => 'Book',
        'add_new'            => 'Add a new one',
        'add_new_item'       => 'Add a new book',
        'edit_item'          => 'Edit the book',
        'new_item'           => 'New book',
        'view_item'          => 'See the book',
        'search_items'       => 'Find a book',
        'not_found'          => 'Books not found',
        'not_found_in_trash' => 'No books found in the trash',
        'parent_item_colon'  => 'Parent Pages:',
        'menu_name'          => 'Books'
    ];

    /**
     * Default properties. Will be override with user's properties.
     * 
     * @var array
     */
    protected $properties = [
        'menu_icon'          => 'dashicons-admin-site',
        'show_in_rest'       => true,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => ['slug' => 'world-books'],
        'capability_type'    => 'page', // 'post'
        'capability'         => 'manage_options',
        'has_archive'        => true,
        'hierarchical'       => true, // false
        'menu_position'      => null,
        'supports'           => ['title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'page-attributes']
    ];

    public function __construct(string $postType)
    {

        $this->postType = $postType;
    }

    /**
     * Parse args, rewrite or complete the default ones.
     * 
     * @param array $labels   CPT args.
     * 
     * @return object
     */
    public function labels(array $labels): object
    {

        $this->labels = wp_parse_args($labels, $this->labels);

        return $this;
    }

    /**
     * Parse args, rewrite or complete the default ones.
     * 
     * @param array $properties   CPT args.
     * 
     * @return object
     */
    public function properties(array $properties): object
    {

        if (is_array($properties)) {

            $this->properties = wp_parse_args($properties, $this->properties);
        }

        return $this;
    }

    /**
     * Register Custom Post Type
     * 
     * @return void
     */
    public function register(): void
    {

        add_action('init', function () {

            $options = [
                'labels' => $this->labels,
            ];

            $options = array_merge($options, $this->properties);

            register_post_type($this->postType, $options);

            $this->activate();
        });
    }

    /**
     * Activate the Custom Post Type.
     * If not to use this function,
     * there will be page 404 on the 
     * single CPT post.
     * 
     * @return void
     */
    public function activate(): void
    {

        $cptOption = "{$this->uniqueString}_{$this->postType}";

        if (!get_option($cptOption)) {

            flush_rewrite_rules();

            update_option($cptOption, 'activated');
        }
    }
}
